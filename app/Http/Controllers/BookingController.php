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
    // Fetch active courts for display
    public function index(): View
    {
        $courts = Court::where('status', 'active')->orderBy('id')->get();
        return view('welcome', compact('courts'));
    }

    // Store a new booking with receipt upload support
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'        => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'contact_number'   => 'required|string|max:50',
            'court_id'         => 'required|exists:courts,id',
            'booking_date'     => 'required|date|after_or_equal:today',
            'start_time'       => 'required|date_format:H:i,H:i:s',
            'end_time'         => 'required|date_format:H:i,H:i:s|after:start_time',
            'total_amount'     => 'required|numeric|min:0',
            'payment_reference'=> 'nullable|string|max:100',
            'receipt'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // 1. Strict Double-Booking Overlap Check
        if (Booking::hasOverlap($validated['court_id'], $validated['booking_date'], $validated['start_time'], $validated['end_time'])) {
            return back()->withErrors(['time_slot' => 'The selected court is already booked for this time slot. Please choose another time or court.'])->withInput();
        }

        // 2. Handle Receipt Upload
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        // 3. Create or find Customer
        $customer = Customer::firstOrCreate(
            ['contact_number' => $validated['contact_number']],
            [
                'full_name' => $validated['full_name'],
                'email'     => $validated['email'],
            ]
        );

        // Calculate duration in hours
        $start = \Carbon\Carbon::parse($validated['start_time']);
        $end = \Carbon\Carbon::parse($validated['end_time']);
        $durationHours = $end->diffInHours($start);

        // 4. Create Booking
        $reference = $this->generateBookingReference();
        $booking = Booking::create([
            'booking_reference' => $reference,
            'customer_id'       => $customer->id,
            'court_id'          => $validated['court_id'],
            'booking_date'      => $validated['booking_date'],
            'start_time'        => $validated['start_time'],
            'end_time'          => $validated['end_time'],
            'duration'          => $durationHours > 0 ? $durationHours : 1,
            'total_amount'      => $validated['total_amount'],
            'payment_reference' => $validated['payment_reference'] ?? null,
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

        return view('welcome', [
            'courts' => Court::query()
                ->where('status', 'active')
                ->orderBy('id')
                ->get(),
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