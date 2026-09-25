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

        $bookings = Booking::with(['customer', 'court'])
            ->orderBy('created_at', 'desc')
            ->get();

        $courts = Court::orderBy('id')->get();

        return response()
            ->view('admin.dashboard', compact(
                'totalBookings',
                'pendingCount',
                'confirmedCount',
                'todaysCount',
                'cancelledCount',
                'totalRevenue',
                'bookings',
                'courts'
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
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

        $hasOverlap = Booking::hasOverlap(
            $validated['court_id'],
            $validated['booking_date'],
            $validated['start_time'],
            $validated['end_time']
        );

        if ($hasOverlap) {
            return redirect()->back()->with('error', 'This time slot is already booked for the selected court.')->withInput();
        }

        if (!empty($validated['contact_number'])) {
            $customer = Customer::updateOrCreate(
                ['contact_number' => $validated['contact_number']],
                ['full_name' => $validated['customer_name']]
            );
        } else {
            $customer = Customer::create([
                'full_name'      => $validated['customer_name'],
                'contact_number' => 'N/A',
            ]);
        }

        $reference = 'WK-' . strtoupper(Str::random(6));

        $start = Carbon::parse($validated['start_time']);
        $end   = Carbon::parse($validated['end_time']);
        $duration = $start->diffInHours($end) ?: 1;

        Booking::create([
            'booking_reference' => $reference,
            'customer_id'       => $customer->id,
            'court_id'          => $validated['court_id'],
            'customer_name'     => $validated['customer_name'],
            'booking_date'      => $validated['booking_date'],
            'start_time'        => $validated['start_time'],
            'end_time'          => $validated['end_time'],
            'duration'          => $duration,
            'total_price'       => $validated['total_price'],
            'total_amount'      => $validated['total_price'],
            'payment_method'    => 'Cash / Walk-in',
            'payment_status'    => $validated['payment_status'],
            'booking_status'    => $validated['booking_status'],
        ]);

        return redirect()->back()->with('success', 'Walk-in booking added! Reference: ' . $reference);
    }

    // Approve Payment
    public function approvePayment(Request $request, Booking $booking)
    {
        $booking->update([
            'payment_status' => 'Verified',
            'booking_status' => 'Confirmed',
            'rejection_reason' => null,
        ]);

        $message = "Booking {$booking->booking_reference} approved & confirmed successfully.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'booking' => $booking->fresh(['customer', 'court']),
            ]);
        }

        return back()->with('success', $message);
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

        $message = "Booking {$booking->booking_reference} has been rejected.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'booking' => $booking->fresh(['customer', 'court']),
            ]);
        }

        return back()->with('success', $message);
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

    public function deleteBooking(Request $request, Booking $booking)
    {
        $bookingId = $booking->id;
        $booking->delete();

        $message = 'Booking deleted permanently.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'id' => $bookingId,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Approve every currently-pending booking belonging to one customer
     * in a single request.
     */
    public function verifyAllForCustomer(Request $request, Customer $customer)
{
    $courtId = $request->query('court_id');
    $customerName = $request->query('customer_name');

    $query = Booking::where('customer_id', $customer->id)
        ->where('booking_status', 'Pending Verification');

    if ($courtId) {
        $query->where('court_id', $courtId);
    }

    if ($customerName) {
        $query->where('customer_name', $customerName);
    }

    $updated = $query->update([
        'payment_status'   => 'Verified',
        'booking_status'   => 'Confirmed',
        'rejection_reason' => null,
    ]);

    $bookings = Booking::where('customer_id', $customer->id)->with(['customer', 'court'])->get();

    $message = $updated > 0
        ? "{$updated} booking(s) approved for {$customer->full_name}."
        : "No pending bookings found.";

    if ($request->wantsJson()) {
        return response()->json(['success' => true, 'message' => $message, 'bookings' => $bookings]);
    }

    return back()->with('success', $message);
}

    /**
     * Reject every currently-pending booking belonging to one customer
     * in a single request, with one shared rejection reason applied to all.
     */
    public function rejectAllForCustomer(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $updated = Booking::where('customer_id', $customer->id)
            ->where('booking_status', 'Pending Verification')
            ->update([
                'payment_status'   => 'Rejected',
                'booking_status'   => 'Rejected',
                'rejection_reason' => $validated['rejection_reason'],
            ]);

        $bookings = Booking::where('customer_id', $customer->id)
            ->with(['customer', 'court'])
            ->get();

        $message = $updated > 0
            ? "{$updated} booking(s) rejected for {$customer->full_name}."
            : "No pending bookings found for {$customer->full_name}.";

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => $message,
                'bookings' => $bookings,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Delete every booking in the system, regardless of any filter the
     * admin currently has applied on the dashboard. Destructive and
     * irreversible — the frontend requires typing a confirmation phrase
     * before this endpoint is ever called.
     */
    public function deleteAllBookings(Request $request)
    {
        $count = Booking::count();

        Booking::query()->delete();

        $message = "{$count} booking(s) deleted permanently.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}