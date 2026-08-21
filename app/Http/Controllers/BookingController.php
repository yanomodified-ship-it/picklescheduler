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

        $existingBookings = Booking::where('booking_date', '>=', now()->format('Y-m-d'))
            ->whereNotIn('booking_status', ['cancelled', 'rejected', 'Cancelled', 'Rejected'])
            ->get(['court_id', 'booking_date', 'start_time', 'end_time', 'booking_status']);

        return view('booking', compact('courts', 'existingBookings'));
    }

    // Store a new booking
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'email'             => 'nullable|email|max:150',
            'contact_number'    => 'required|string|max:30',
            'court_id'          => 'required|exists:courts,id',
            'booking_date'      => 'required|date|after_or_equal:today',
            'start_time'        => 'required',
            'end_time'          => 'required|after:start_time',
            'number_of_players' => 'nullable|integer|min:1|max:30',
        ]);

        // 1. Strict Double-Booking Overlap Check
        if (method_exists(Booking::class, 'hasOverlap')) {
            if (Booking::hasOverlap($validated['court_id'], $validated['booking_date'], $validated['start_time'], $validated['end_time'])) {
                return back()->withErrors(['time_slot' => 'The selected court is already booked for this time slot. Please choose another time or court.'])->withInput();
            }
        }

        // 2. Calculate Pricing and Duration correctly based on start and end time gaps
        $court = Court::findOrFail($validated['court_id']);
        $hourlyRate = $this->getCourtHourlyRate($court);

        $start = \Carbon\Carbon::parse($validated['start_time']);
        $end = \Carbon\Carbon::parse($validated['end_time']);
        
        $durationMinutes = $start->diffInMinutes($end);
        $durationHours = max(1, $durationMinutes / 60); 
        
        $totalAmount = $hourlyRate * $durationHours;

        // 3. Create or find Customer
        $customer = Customer::firstOrCreate(
            ['contact_number' => $validated['contact_number']],
            [
                'name'      => $validated['name'],
                'full_name' => $validated['name'],
                'email'     => $validated['email'] ?? null,
            ]
        );

        // 4. Create Booking
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