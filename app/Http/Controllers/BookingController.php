<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    // Fetch active courts and future/today bookings for availability display
    public function index(): View
    {
        $courts = Court::where('status', 'active')->orderBy('id')->get();

        $existingBookings = Booking::where('booking_date', '>=', now()->format('Y-m-d'))
            ->whereNotIn('booking_status', ['cancelled', 'rejected', 'Cancelled', 'Rejected'])
            ->get(['court_id', 'booking_date', 'start_time', 'end_time', 'booking_status']);

        return view('welcome', compact('courts', 'existingBookings'));
    }

    // Store a new booking with receipt upload support
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:100',
            'email'              => 'nullable|email|max:150',
            'contact_number'     => 'required|string|max:30',
            'court_id'           => 'required|exists:courts,id',
            'booking_date'       => 'required|date|after_or_equal:today',
            'start_time'         => 'required',
            'end_time'           => 'required|after:start_time',
            'number_of_players'  => 'nullable|integer|min:1|max:30',
            'payment_reference' => 'required|string|max:100',
            'payment_receipt'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // 1. Strict Double-Booking Overlap Check
        if (method_exists(Booking::class, 'hasOverlap')) {
            if (Booking::hasOverlap($validated['court_id'], $validated['booking_date'], $validated['start_time'], $validated['end_time'])) {
                return back()->withErrors(['time_slot' => 'The selected court is already booked for this time slot. Please choose another time or court.'])->withInput();
            }
        }

        // 2. Calculate Pricing based on duration
        $court = Court::findOrFail($validated['court_id']);
        $hourlyRate = $this->getCourtHourlyRate($court);

        $start = \Carbon\Carbon::parse($validated['start_time']);
        $end = \Carbon\Carbon::parse($validated['end_time']);
        $durationMinutes = $end->diffInMinutes($start);
        $durationHours = max(0.5, $durationMinutes / 60);
        $totalAmount = $hourlyRate * $durationHours;

        // 3. Handle Receipt Upload
        $receiptPath = null;
        if ($request->hasFile('payment_receipt')) {
            $receiptPath = $request->file('payment_receipt')->store('receipts', 'public');
        }

        // 4. Create or find Customer
        $customer = Customer::firstOrCreate(
            ['contact_number' => $validated['contact_number']],
            [
                'name'  => $validated['name'],
                'email' => $validated['email'] ?? null,
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
            'payment_reference' => $validated['payment_reference'],
            'receipt_path'      => $receiptPath,
            'booking_status'    => 'Pending Verification',
            'payment_status'    => 'For Verification',
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

        $courts = Court::where('status', 'active')->orderBy('id')->get();

        $existingBookings = Booking::where('booking_date', '>=', now()->format('Y-m-d'))
            ->whereNotIn('booking_status', ['cancelled', 'rejected', 'Cancelled', 'Rejected'])
            ->get(['court_id', 'booking_date', 'start_time', 'end_time', 'booking_status']);

        return view('welcome', [
            'courts'              => $courts,
            'existingBookings'    => $existingBookings,
            'confirmationBooking' => $booking,
        ]);
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
     * Resolve the hourly rate.
     */
    private function getCourtHourlyRate(Court $court): float
    {
        foreach (['price_per_hour', 'hourly_rate', 'rate'] as $attribute) {
            if (array_key_exists($attribute, $court->getAttributes()) && $court->{$attribute} !== null) {
                return (float) $court->{$attribute};
            }
        }

        return (float) env('PICKLEBALL_HOURLY_RATE', 500);
    }
}