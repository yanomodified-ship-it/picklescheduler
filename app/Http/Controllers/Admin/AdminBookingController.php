<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use Illuminate\Http\Request;

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