<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Home page
     */
    public function index(): View
    {
        $courts = Court::where('status', 'active')
            ->with([
                'bookings' => function ($query) {
                    $query->whereDate('booking_date', today())
                        ->where('booking_status', 'Confirmed');
                }
            ])
            ->orderBy('id')
            ->get();

        return view('welcome', compact('courts'));
    }

    /**
     * Booking page
     */
    public function create(): View
    {
        $courts = Court::where('status', 'active')
            ->orderBy('id')
            ->get();

        /*
         * Get bookings needed by the booking page.
         *
         * Only non-cancelled/non-rejected bookings are relevant.
         */
        $existingBookings = Booking::where(
                'booking_date',
                '>=',
                now()->format('Y-m-d')
            )
            ->whereNotIn('booking_status', [
                'Cancelled',
                'Rejected'
            ])
            ->get([
                'court_id',
                'booking_date',
                'start_time',
                'end_time',
                'booking_status'
            ]);

        return view(
            'booking',
            compact('courts', 'existingBookings')
        );
    }

    /**
     * Store a booking
     */
    public function store(Request $request)
    {
        /*
         * ---------------------------------------------------------
         * 1. VALIDATE INPUT
         * ---------------------------------------------------------
         */
        $validated = $request->validate([
            'booking_date'      => 'required|date|after_or_equal:today',
            'court_id'          => 'required|exists:courts,id',
            'slots'             => 'required|array|min:1',
            'slots.*'           => 'date_format:H:i|after_or_equal:05:00',
            'name'              => 'required|string|max:100',
            'email'             => 'nullable|email|max:255',
            'contact_number'    => 'required|string|max:30',
            'number_of_players' => 'required|integer|min:1|max:30',
            'payment_method'    => 'required|in:GCash,Bank',
        ]);

        /*
         * ---------------------------------------------------------
         * 2. CLEAN SELECTED SLOTS
         * ---------------------------------------------------------
         */
        $slots = collect($validated['slots'])
            ->unique()
            ->sort()
            ->values();

        /*
         * ---------------------------------------------------------
         * 3. GET COURT
         * ---------------------------------------------------------
         */
        $court = Court::findOrFail($validated['court_id']);

        /*
         * Make sure the court is active.
         */
        if ($court->status !== 'active') {
            return back()
                ->withErrors([
                    'court_id' =>
                        'This court is not currently available for booking.'
                ])
                ->withInput();
        }

        /*
         * ---------------------------------------------------------
         * 4. CHECK OPERATING HOURS
         * ---------------------------------------------------------
         */
        $startHour = (int) substr(
            $court->operating_hours_start,
            0,
            2
        );

        $endHour = (int) substr(
            $court->operating_hours_end,
            0,
            2
        );

        /*
         * 00:00 means the court is open until the end of the day.
         */
        if (
            $endHour === 0 &&
            str_starts_with(
                $court->operating_hours_end,
                '00'
            )
        ) {
            $endHour = 24;
        }

        foreach ($slots as $slot) {

            $slotHour = (int) substr($slot, 0, 2);

            if (
                $slotHour < $startHour ||
                $slotHour > $endHour
            ) {
                return back()
                    ->withErrors([
                        'time_slot' =>
                            "The {$slot} slot is outside this court's operating hours."
                    ])
                    ->withInput();
            }
        }

        /*
         * ---------------------------------------------------------
         * 5. CREATE / UPDATE CUSTOMER
         * ---------------------------------------------------------
         */
        $customer = Customer::updateOrCreate(
            [
                'contact_number' => $validated['contact_number'],
            ],
            [
                'full_name' => $validated['name'],
                'email'     => $validated['email'] ?? null,
            ]
        );

        /*
         * ---------------------------------------------------------
         * 6. GENERATE BOOKING REFERENCE
         * ---------------------------------------------------------
         */
        $baseReference = $this->generateBookingReference();

        /*
         * Store IDs of newly created bookings.
         *
         * This allows us to retrieve exactly the bookings we just
         * created instead of doing:
         *
         * WHERE booking_reference LIKE 'PKL-XXXXXX%'
         */
        $createdBookingIds = [];

        try {

            /*
             * -----------------------------------------------------
             * 7. DATABASE TRANSACTION
             * -----------------------------------------------------
             *
             * The transaction is important because two customers
             * might try to book the same court/time simultaneously.
             */
            DB::transaction(function () use (
                $slots,
                $validated,
                $customer,
                $baseReference,
                &$createdBookingIds
            ) {

                /*
                 * LOCK THE COURT ROW
                 *
                 * This prevents simultaneous transactions from
                 * booking the same court at the same time.
                 */
                Court::where('id', $validated['court_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                 * -------------------------------------------------
                 * GET EXISTING BOOKINGS ONCE
                 * -------------------------------------------------
                 *
                 * Instead of querying the database for every slot,
                 * get all bookings for this court/date once.
                 */
                $existingBookings = Booking::where(
                        'court_id',
                        $validated['court_id']
                    )
                    ->where(
                        'booking_date',
                        $validated['booking_date']
                    )
                    ->whereNotIn(
                        'booking_status',
                        [
                            'Rejected',
                            'Cancelled'
                        ]
                    )
                    ->get([
                        'start_time',
                        'end_time'
                    ]);

                /*
                 * -------------------------------------------------
                 * CHECK SLOT OVERLAPS IN PHP
                 * -------------------------------------------------
                 *
                 * This avoids additional database queries.
                 */
                foreach ($slots as $slot) {

                    $slotStart = Carbon::parse($slot);

                    $slotEnd = $slotStart
                        ->copy()
                        ->addHour();

                    foreach ($existingBookings as $existing) {

                        $existingStart = Carbon::parse(
                            $existing->start_time
                        );

                        $existingEnd = Carbon::parse(
                            $existing->end_time
                        );

                        /*
                         * Check if the requested slot overlaps
                         * an existing booking.
                         */
                        if (
                            $existingStart->lt($slotEnd) &&
                            $existingEnd->gt($slotStart)
                        ) {
                            throw new \RuntimeException(
                                "The {$slot} slot is already booked for this court. Please choose another time or court."
                            );
                        }
                    }
                }

                /*
                 * -------------------------------------------------
                 * CREATE BOOKINGS
                 * -------------------------------------------------
                 */
                $totalSlots = $slots->count();

                foreach (
                    $slots as $index => $slot
                ) {

                    $slotStartHour = (int) Carbon::parse(
                        $slot
                    )->format('H');

                    /*
                     * Daytime:
                     * 05:00 - 16:59 = ₱150
                     *
                     * Evening:
                     * 17:00 onwards = ₱300
                     */
                    $rate = (
                        $slotStartHour >= 5 &&
                        $slotStartHour < 17
                    )
                        ? 150
                        : 300;

                    /*
                     * Handle 11 PM slot.
                     */
                    $slotEnd = (
                        $slotStartHour === 23
                    )
                        ? '23:59'
                        : Carbon::parse($slot)
                            ->addHour()
                            ->format('H:i');

                    /*
                     * If multiple slots are selected:
                     *
                     * PKL-ABC123-1
                     * PKL-ABC123-2
                     * PKL-ABC123-3
                     *
                     * If only one slot:
                     *
                     * PKL-ABC123
                     */
                    $uniqueSlotReference =
                        $totalSlots > 1
                            ? "{$baseReference}-" .
                                ($index + 1)
                            : $baseReference;

                    /*
                     * Create booking.
                     */
                    $booking = Booking::create([
                        'booking_reference' =>
                            $uniqueSlotReference,

                        'customer_id' =>
                            $customer->id,

                        'court_id' =>
                            $validated['court_id'],

                        'booking_date' =>
                            $validated['booking_date'],

                        'start_time' =>
                            $slot,

                        'end_time' =>
                            $slotEnd,

                        'number_of_players' =>
                            $validated['number_of_players'] ?? 2,

                        'duration' => 1,

                        'total_price' => $rate,

                        'total_amount' => $rate,

                        'booking_status' =>
                            'Pending Verification',

                        'payment_status' =>
                            'For Verification',

                        'payment_method' =>
                            $validated['payment_method'],
                    ]);

                    /*
                     * Save ID so we can retrieve exactly the
                     * records created by this request.
                     */
                    $createdBookingIds[] = $booking->id;
                }
            });

        } catch (\RuntimeException $e) {

            /*
             * Booking conflict.
             */
            return back()
                ->withErrors([
                    'time_slot' => $e->getMessage()
                ])
                ->withInput();

        } catch (\Illuminate\Database\QueryException $e) {

            /*
             * Database-level error/conflict.
             */
            report($e);

            return back()
                ->withErrors([
                    'time_slot' =>
                        'That slot was just booked by someone else, or something went wrong. Please try again.'
                ])
                ->withInput();
        }

        /*
         * ---------------------------------------------------------
         * 8. GET THE BOOKINGS WE JUST CREATED
         * ---------------------------------------------------------
         *
         * This replaces the old:
         *
         * redirect()
         *     ->route('bookings.confirmation', ...)
         *
         * followed by another HTTP request.
         *
         * We stay inside the same request.
         */
        $bookings = Booking::query()
            ->with([
                'court',
                'customer'
            ])
            ->whereIn('id', $createdBookingIds)
            ->orderBy('start_time')
            ->get();

        /*
         * Safety check.
         */
        if ($bookings->isEmpty()) {
            abort(404);
        }

        /*
         * First booking is used by the confirmation page.
         */
        $booking = $bookings->first();

        /*
         * Show the base reference instead of:
         *
         * PKL-ABC123-1
         *
         * PKL-ABC123-2
         *
         */
        $booking->booking_reference =
            $baseReference;

        /*
         * ---------------------------------------------------------
         * 9. CALCULATE TOTAL
         * ---------------------------------------------------------
         */
        $totalAmount = $bookings->sum(
            fn ($booking) => (float) (
                $booking->total_price ??
                $booking->total_amount ??
                0
            )
        );

        /*
         * ---------------------------------------------------------
         * 10. DIRECTLY SHOW CONFIRMATION
         * ---------------------------------------------------------
         *
         * IMPORTANT:
         *
         * There is NO redirect here.
         *
         * Old flow:
         *
         * POST /bookings
         *       ↓
         *      302
         *       ↓
         * GET /confirmation/...
         *
         * New flow:
         *
         * POST /bookings
         *       ↓
         * confirmation view
         */
        return view(
            'confirmation',
            compact(
                'booking',
                'bookings',
                'totalAmount'
            )
        );
    }

    /**
     * Confirmation page.
     *
     * This route is still kept so an existing confirmation URL
     * can still be opened directly.
     */
    public function confirmation(
        string $booking_reference
    ): View {

        /*
         * Find bookings belonging to this reference.
         */
        $bookings = Booking::query()
            ->with([
                'court',
                'customer'
            ])
            ->where(
                'booking_reference',
                'LIKE',
                $booking_reference . '%'
            )
            ->orderBy('start_time')
            ->get();

        /*
         * Booking not found.
         */
        if ($bookings->isEmpty()) {
            abort(404);
        }

        /*
         * First booking.
         */
        $booking = $bookings->first();

        /*
         * Display base reference.
         */
        $booking->booking_reference =
            $booking_reference;

        /*
         * Calculate total.
         */
        $totalAmount = $bookings->sum(
            fn ($booking) => (float) (
                $booking->total_price ??
                $booking->total_amount ??
                0
            )
        );

        return view(
            'confirmation',
            compact(
                'booking',
                'bookings',
                'totalAmount'
            )
        );
    }

    /**
     * Generate a unique booking reference.
     *
     * Example:
     *
     * PKL-4F8A21
     */
    private function generateBookingReference(): string
    {
        do {

            $reference =
                'PKL-' .
                strtoupper(
                    Str::random(6)
                );

            /*
             * Check the whole reference prefix.
             *
             * This also catches:
             *
             * PKL-ABC123
             * PKL-ABC123-1
             * PKL-ABC123-2
             */
            $exists = Booking::query()
                ->where(
                    'booking_reference',
                    'LIKE',
                    $reference . '%'
                )
                ->exists();

        } while ($exists);

        return $reference;
    }

    /**
     * Resolve hourly rate dynamically.
     *
     * Currently kept for compatibility with the existing
     * controller structure.
     */
    private function getCourtHourlyRate(
        Court $court
    ): float {
        return 500.00;
    }
}