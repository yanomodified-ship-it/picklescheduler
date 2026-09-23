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
        $courts = Court::where('status', 'active')->orderBy('id')->get();

        // Fetch CONFIRMED and PENDING bookings so the picker can grey out
        // confirmed slots and clearly flag pending ones (only cancelled/
        // rejected bookings should free up a slot again).
        $existingBookings = Booking::where('booking_date', '>=', now()->format('Y-m-d'))
            ->whereNotIn('booking_status', ['Cancelled', 'Rejected'])
            ->get(['court_id', 'booking_date', 'start_time', 'end_time', 'booking_status']);

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

        // Clean up the selected hour slots: unique + sorted (e.g. ["06:00","09:00"])
        $slots = collect($validated['slots'])
            ->unique()
            ->sort()
            ->values();

        // STEP 13: Confirm the court is active and every slot falls within its
        // operating hours. This mirrors the frontend's own slot-generating
        // loop in booking.blade.php exactly: it compares whole hours, and it
        // is INCLUSIVE of the closing hour itself (a court closing at 23:00
        // still offers a bookable 11 PM–12 AM slot), and a closing hour of
        // "00:00" means "open through the end of the day" (treated as 24).
        $court = Court::findOrFail($validated['court_id']);

        if ($court->status !== 'active') {
            return back()
                ->withErrors(['court_id' => 'This court is not currently available for booking.'])
                ->withInput();
        }

        $startHour = (int) substr($court->operating_hours_start, 0, 2);
        $endHour   = (int) substr($court->operating_hours_end, 0, 2);

        if ($endHour === 0 && str_starts_with($court->operating_hours_end, '00')) {
            $endHour = 24;
        }

        foreach ($slots as $slot) {
            $slotHour = (int) substr($slot, 0, 2);

            if ($slotHour < $startHour || $slotHour > $endHour) {
                return back()
                    ->withErrors(['time_slot' => "The {$slot} slot is outside this court's operating hours."])
                    ->withInput();
            }
        }

        // 2. Create or update the Customer.
        // IMPORTANT: use updateOrCreate (not firstOrCreate) so that if this
        // contact number already has a customer record, the name/email are
        // refreshed to what was just typed. firstOrCreate would silently keep
        // whatever name was saved the very first time that number was used.
        $customer = Customer::updateOrCreate(
            ['contact_number' => $validated['contact_number']],
            [
                'full_name' => $validated['name'],
                'email'     => $validated['email'] ?? null,
            ]
        );

        // 3. Generate base booking reference
        $baseReference = $this->generateBookingReference();

        // STEP 12: The overlap check now happens INSIDE the transaction, with
        // a row lock on this court+date, so two concurrent requests can no
        // longer both pass the check and both create a booking for the same
        // slot. If a conflict is found, we throw to trigger a rollback and
        // catch it right after to show the same error message as before.
        try {
            DB::transaction(function () use ($slots, $validated, $customer, $baseReference) {

                // Lock the court row itself. Unlike locking existing bookings,
                // the court row is guaranteed to exist even when this is the
                // very first booking for this date, so this reliably
                // serializes concurrent requests for the same court no
                // matter what (or how little) is already booked.
                Court::where('id', $validated['court_id'])->lockForUpdate()->firstOrFail();

                foreach ($slots as $slot) {
                    $slotEnd = Carbon::parse($slot)->addHour()->format('H:i');

                    if (Booking::hasOverlap($validated['court_id'], $validated['booking_date'], $slot, $slotEnd)) {
                        throw new \RuntimeException("The {$slot} slot is already booked for this court. Please choose another time or court.");
                    }
                }

                $totalSlots = $slots->count();

                foreach ($slots as $index => $slot) {
                    $slotStartHour = (int) Carbon::parse($slot)->format('H');
                    $rate = ($slotStartHour >= 5 && $slotStartHour < 17) ? 150 : 300;

                    $slotEnd = ($slotStartHour === 23)
                        ? '23:59'
                        : Carbon::parse($slot)->addHour()->format('H:i');

                    // Append index suffix if multiple slots selected to prevent database unique constraint violation
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
                ->withErrors(['time_slot' => $e->getMessage()])
                ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            // A real DB-level conflict slipped past the checks above
            // (e.g. a duplicate booking_reference, a deadlock, or a lock
            // timeout under heavy concurrent load). Log it so you can see
            // if/how often it happens, but never let it surface as a raw 500.
            report($e);

            return back()
                ->withErrors(['time_slot' => 'That slot was just booked by someone else, or something went wrong. Please try again.'])
                ->withInput();
        }

        return redirect()->route('bookings.confirmation', ['booking_reference' => $baseReference])
            ->with('success', "Booking submitted successfully!");
    }

    /**
     * Display booking confirmation page.
     */
    public function confirmation(string $booking_reference): View
    {
        // Query all slots using LIKE to catch base reference and appended suffixes (e.g. PKL-4F8A21, PKL-4F8A21-1, PKL-4F8A21-2)
        $bookings = Booking::query()
            ->with(['court', 'customer'])
            ->where('booking_reference', 'LIKE', $booking_reference . '%')
            ->orderBy('start_time')
            ->get();

        if ($bookings->isEmpty()) {
            abort(404);
        }

        $booking = $bookings->first();
        $booking->booking_reference = $booking_reference; // Retain base reference for clean UI display
        $totalAmount = $bookings->sum(fn ($b) => (float) ($b->total_price ?? $b->total_amount ?? 0));

        return view('confirmation', compact('booking', 'bookings', 'totalAmount'));
    }

    /**
     * Generate a unique booking reference such as PKL-4F8A21.
     */
    private function generateBookingReference(): string
    {
        do {
            $reference = 'PKL-' . strtoupper(Str::random(6));

            $exists = Booking::query()
                ->where('booking_reference', 'LIKE', $reference . '%')
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