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
    // Fetch active courts for the home page display
    public function index(): View
    {
        $courts = Court::where('status', 'active')
            ->with(['bookings' => function ($query) {
                $query->whereDate('booking_date', today())
                    ->where('booking_status', 'Confirmed');
            }])
            ->orderBy('id')
            ->get();

        return view('welcome', compact('courts'));
    }

    // Load the dedicated booking form page
    public function create(): View
    {
        $courts = Court::where('status', 'active')
            ->orderBy('id')
            ->get();

        // Fetch confirmed and pending bookings
        // Cancelled and rejected bookings are available again
        $existingBookings = Booking::where(
                'booking_date',
                '>=',
                now()->format('Y-m-d')
            )
            ->whereNotIn('booking_status', ['Cancelled', 'Rejected'])
            ->get([
                'court_id',
                'booking_date',
                'start_time',
                'end_time',
                'booking_status'
            ]);

        return view('booking', compact('courts', 'existingBookings'));
    }

    // Store a new booking
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_date'      => 'required|date|after_or_equal:today',
            'court_id'          => 'required|exists:courts,id',
            'slots'             => 'required|array|min:1',
            'slots.*'           => 'date_format:H:i|after_or_equal:05:00',
            'name'              => 'required|string|max:100',
            'contact_number'    => 'required|string|max:30',
            'number_of_players' => 'required|integer|min:1|max:30',
            'payment_method'    => 'required|in:GCash,Bank',
        ]);

        // Clean selected slots: remove duplicates and sort
        $slots = collect($validated['slots'])
            ->unique()
            ->sort()
            ->values();

        // Get the selected court
        $court = Court::findOrFail($validated['court_id']);

        // Make sure the court is active
        if ($court->status !== 'active') {
            return back()
                ->withErrors([
                    'court_id' => 'This court is not currently available for booking.'
                ])
                ->withInput();
        }

        // Check operating hours
        $startHour = (int) substr($court->operating_hours_start, 0, 2);
        $endHour   = (int) substr($court->operating_hours_end, 0, 2);

        // 00:00 means the court is open through the end of the day
        if (
            $endHour === 0 &&
            str_starts_with($court->operating_hours_end, '00')
        ) {
            $endHour = 24;
        }

        foreach ($slots as $slot) {
            $slotHour = (int) substr($slot, 0, 2);

            if ($slotHour < $startHour || $slotHour > $endHour) {
                return back()
                    ->withErrors([
                        'time_slot' =>
                            "The {$slot} slot is outside this court's operating hours."
                    ])
                    ->withInput();
            }
        }

        // Create or update customer
        $customer = Customer::updateOrCreate(
            [
                'contact_number' => $validated['contact_number']
            ],
            [
                'full_name' => $validated['name'],
                'email'     => $validated['email'] ?? null,
            ]
        );

        // Generate booking reference
        $baseReference = $this->generateBookingReference();

        try {
            DB::transaction(function () use (
                $slots,
                $validated,
                $customer,
                $baseReference
            ) {

                /*
                 * Lock the court row.
                 *
                 * This prevents two people from booking the same
                 * court at the same time.
                 */
                Court::where('id', $validated['court_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                 * IMPORTANT PERFORMANCE CHANGE:
                 *
                 * Get all existing bookings for this court/date
                 * ONCE instead of running Booking::hasOverlap()
                 * once for every selected slot.
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
                        ['Rejected', 'Cancelled']
                    )
                    ->get([
                        'start_time',
                        'end_time'
                    ]);

                /*
                 * Check all selected slots in PHP.
                 *
                 * This avoids multiple database queries.
                 */
                foreach ($slots as $slot) {

                    $slotStart = Carbon::parse($slot);
                    $slotEnd   = $slotStart->copy()->addHour();

                    foreach ($existingBookings as $existing) {

                        $existingStart = Carbon::parse(
                            $existing->start_time
                        );

                        $existingEnd = Carbon::parse(
                            $existing->end_time
                        );

                        // Overlap exists
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

                // Create booking records
                $totalSlots = $slots->count();

                foreach ($slots as $index => $slot) {

                    $slotStartHour = (int) Carbon::parse($slot)->format('H');

                    // Daytime = ₱150
                    // Evening = ₱300
                    $rate = (
                        $slotStartHour >= 5 &&
                        $slotStartHour < 17
                    )
                        ? 150
                        : 300;

                    // Handle 11 PM slot
                    $slotEnd = ($slotStartHour === 23)
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
                     * This keeps booking_reference unique.
                     */
                    $uniqueSlotReference = $totalSlots > 1
                        ? "{$baseReference}-" . ($index + 1)
                        : $baseReference;

                    Booking::create([
                        'booking_reference' => $uniqueSlotReference,
                        'customer_id'       => $customer->id,
                        'court_id'          => $validated['court_id'],
                        'booking_date'      => $validated['booking_date'],
                        'start_time'        => $slot,
                        'end_time'          => $slotEnd,
                        'number_of_players' => $validated['number_of_players'] ?? 2,
                        'duration'          => 1,
                        'total_price'       => $rate,
                        'total_amount'      => $rate,
                        'booking_status'    => 'Pending Verification',
                        'payment_status'    => 'For Verification',
                        'payment_method'    => $validated['payment_method'],
                    ]);
                }
            });

        } catch (\RuntimeException $e) {

            return back()
                ->withErrors([
                    'time_slot' => $e->getMessage()
                ])
                ->withInput();

        } catch (\Illuminate\Database\QueryException $e) {

            // Database-level conflict/error
            report($e);

            return back()
                ->withErrors([
                    'time_slot' =>
                        'That slot was just booked by someone else, or something went wrong. Please try again.'
                ])
                ->withInput();
        }

        // Send customer to confirmation page
        return redirect()
            ->route(
                'bookings.confirmation',
                [
                    'booking_reference' => $baseReference
                ]
            )
            ->with(
                'success',
                'Booking submitted successfully!'
            );
    }

    /**
     * Display booking confirmation page.
     */
    public function confirmation(string $booking_reference): View
    {
        // Get all slots belonging to this booking reference
        $bookings = Booking::query()
            ->with(['court', 'customer'])
            ->where(
                'booking_reference',
                'LIKE',
                $booking_reference . '%'
            )
            ->orderBy('start_time')
            ->get();

        if ($bookings->isEmpty()) {
            abort(404);
        }

        $booking = $bookings->first();

        // Display the base reference without the -1, -2 suffix
        $booking->booking_reference = $booking_reference;

        // Calculate total amount
        $totalAmount = $bookings->sum(
            fn ($b) => (float) (
                $b->total_price ??
                $b->total_amount ??
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
     * PKL-4F8A21
     */
    private function generateBookingReference(): string
    {
        do {

            $reference = 'PKL-' .
                strtoupper(
                    Str::random(6)
                );

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
     * Resolve the hourly rate dynamically.
     */
    private function getCourtHourlyRate(Court $court): float
    {
        return 500.00;
    }
}