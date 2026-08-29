<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    public function dashboard()
    {
        // Metric Calculations
        $totalBookings      = Booking::count();
        $pendingCount       = Booking::where('payment_status', 'For Verification')->orWhere('booking_status', 'Pending Verification')->count();
        $confirmedCount     = Booking::where('booking_status', 'Confirmed')->count();
        $todaysCount        = Booking::whereDate('booking_date', now()->format('Y-m-d'))->count();
        $cancelledCount     = Booking::whereIn('booking_status', ['Cancelled', 'Rejected'])->count();
        $totalRevenue       = Booking::where('payment_status', 'Verified')->sum('total_price');

        // Fetch All Bookings with relationships
        $bookings = Booking::with(['customer', 'court'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch Courts for Court Management tab
        $courts = Court::orderBy('id')->get();

        return view('admin.dashboard', compact(
            'totalBookings',
            'pendingCount',
            'confirmedCount',
            'todaysCount',
            'cancelledCount',
            'totalRevenue',
            'bookings',
            'courts'
        ));
    }
    
    // Store Walk-in Booking
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'court_id'       => 'required|exists:courts,id',
            'booking_date'   => 'required|date',
            'start_time'     => 'required|string',
            'end_time'       => 'required|string',
            'total_price'    => 'required|numeric|min:0',
            'payment_status' => 'required|string',
            'booking_status' => 'required|string',
        ]);

        // 1. Prevent overlapping bookings
        $hasOverlap = Booking::hasOverlap(
            $validated['court_id'],
            $validated['booking_date'],
            $validated['start_time'],
            $validated['end_time']
        );

        if ($hasOverlap) {
            return redirect()->back()->with('error', 'This time slot is already booked for the selected court.')->withInput();
        }

        // 2. Create or retrieve the customer record
        $customer = Customer::firstOrCreate(
            ['full_name' => $validated['customer_name']],
            ['contact_number' => $validated['contact_number'] ?? 'N/A']
        );

        // 3. Generate a unique reference number
        $reference = 'WK-' . strtoupper(Str::random(6));

        // 4. Calculate duration in hours
        $start = Carbon::parse($validated['start_time']);
        $end   = Carbon::parse($validated['end_time']);
        $duration = $start->diffInHours($end) ?: 1;

        // 5. Save the booking
        Booking::create([
            'booking_reference' => $reference,
            'customer_id'       => $customer->id,
            'court_id'          => $validated['court_id'],
            'booking_date'      => $validated['booking_date'],
            'start_time'        => $validated['start_time'],
            'end_time'          => $validated['end_time'],
            'duration'          => $duration,
            'total_price'       => $validated['total_price'],
            'total_amount'      => $validated['total_price'], // Included to fulfill NOT NULL column constraint
            'payment_method'    => 'Cash / Walk-in',
            'payment_status'    => $validated['payment_status'],
            'booking_status'    => $validated['booking_status'],
        ]);

        return redirect()->back()->with('success', 'Walk-in booking added! Reference: ' . $reference);
    }

    // Approve Payment
    public function approvePayment(Booking $booking)
    {
        $booking->update([
            'payment_status' => 'Verified',
            'booking_status' => 'Confirmed',
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Booking {$booking->booking_reference} approved & confirmed successfully.");
    }

    // Reject Payment with Reason
    public function rejectPayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $booking->update([
            'payment_status'   => 'Rejected',
            'booking_status'   => 'Rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', "Booking {$booking->booking_reference} has been rejected.");
    }

    // Direct Status Changes
    public function confirmBooking(Booking $booking)
    {
        $booking->update(['booking_status' => 'Confirmed', 'payment_status' => 'Verified']);
        return back()->with('success', 'Booking manually confirmed.');
    }

    public function cancelBooking(Booking $booking)
    {
        $booking->update(['booking_status' => 'Cancelled']);
        return back()->with('success', 'Booking cancelled.');
    }

    public function deleteBooking(Booking $booking)
    {
        $booking->delete();
        return back()->with('success', 'Booking deleted permanently.');
    }
}