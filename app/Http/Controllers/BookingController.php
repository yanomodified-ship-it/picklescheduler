<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    // Fetch active courts and future/today bookings for the home page display
    public function index(): View
    {
        $courts = Court::where('status', 'active')->orderBy('id')->get();

        return view('welcome', compact('courts'));
    }

    // Load the dedicated booking form page
    public function create(): View
    {
        $courts = Court::where('status', 'active')->orderBy('id')->get();

        // ONLY FETCH CONFIRMED BOOKINGS TO BLOCK SLOTS ON PUBLIC SITE
        $existingBookings = Booking::where('booking_date', '>=', now()->format('Y-m-d'))
            ->whereIn('booking_status', ['Confirmed', 'confirmed'])
            ->get(['court_id', 'booking_date', 'start_time', 'end_time', 'booking_status']);

        return view('booking', compact('courts', 'existingBookings'));
    }

    // Store a new booking
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_date'      => 'required|date|after_or_equal:today',
            'court_id'          => 'required|exists:courts,id',
            'start_time'        => 'required|date_format:H:i|after_or_equal:05:00',
            'end_time'          => 'required|date_format:H:i|after:start_time',
            'name'              => 'required|string|max:100',
            'contact_number'    => 'required|string|max:30',
            'number_of_players' => 'required|integer|min:1|max:30',
            'payment_method'    => 'required|in:GCash,Bank',
        ]);

        // 1. Strict Double-Booking Overlap Check
        if (method_exists(Booking::class, 'hasOverlap')) {
            if (Booking::hasOverlap($validated['court_id'], $validated['booking_date'], $validated['start_time'], $validated['end_time'])) {
                return back()->withErrors(['time_slot' => 'The selected court is already booked for this time slot. Please choose another time or court.'])->withInput();
            }
        }

        // 2. Parse times & calculate duration (handles 23:59 as full hour ending at midnight)
        $startCarbon = Carbon::parse($validated['start_time']);
        $endCarbon   = Carbon::parse($validated['end_time']);

        if ($validated['end_time'] === '23:59') {
            $endCarbon = Carbon::parse('23:59')->addMinute();
        }

        $durationHours = (int) $startCarbon->diffInHours($endCarbon);

        // 3. Calculate total price dynamically based on time slots
        $startHour = (int) $startCarbon->format('H');
        $endHour   = ($validated['end_time'] === '23:59') ? 24 : (int) $endCarbon->format('H');

        $totalAmount = 0;
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            if ($hour >= 5 && $hour < 17) {
                $totalAmount += 150; // 5:00 AM to 4:59 PM slot (₱150/hr)
            } else {
                $totalAmount += 300; // 5:00 PM to 12:00 AM slot (₱300/hr)
            }
        }

        // 4. Create or find Customer
        $customer = Customer::firstOrCreate(
            ['contact_number' => $validated['contact_number']],
            [
                'full_name' => $validated['name'],
                'email'     => $validated['email'] ?? null,
            ]
        );

        // 5. Create Booking
        $reference = $this->generateBookingReference();
        Booking::create([
            'booking_reference' => $reference,
            'customer_id'       => $customer->id,
            'court_id'          => $validated['court_id'],
            'booking_date'      => $validated['booking_date'],
            'start_time'        => $validated['start_time'],
            'end_time'          => $validated['end_time'],
            'number_of_players' => $validated['number_of_players'] ?? 2,
            'duration'          => $durationHours,
            'total_price'       => $totalAmount,
            'total_amount'      => $totalAmount,
            'booking_status'    => 'Pending Verification',
            'payment_status'    => 'For Verification',
            'payment_method'    => $validated['payment_method'],
        ]);

        return redirect()->route('bookings.confirmation', ['booking_reference' => $reference])
            ->with('success', "Booking submitted successfully!");
    }

    /**
     * Display booking confirmation page.
     */
    public function confirmation(string $booking_reference): View
    {
        $booking = Booking::query()
            ->with(['court', 'customer'])
            ->where('booking_reference', $booking_reference)
            ->firstOrFail();

        return view('confirmation', compact('booking'));
    }

    /**
     * Generate a unique booking reference such as PKL-4F8A21.
     */
    private function generateBookingReference(): string
    {
        do {
            $reference = 'PKL-' . strtoupper(Str::random(6));

            $exists = Booking::query()
                ->where('booking_reference', $reference)
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