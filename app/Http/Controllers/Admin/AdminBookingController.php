<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\OpenPlaySession;
use App\Models\OpenPlayEntry;
use App\Support\Pricing;

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

        [$openPlayUpcoming, $openPlayPast] = OpenPlaySession::adminLists();
        $openPlayStats = OpenPlayEntry::revenueStats();
        $openPlayPastTotals = OpenPlaySession::pastTotals();
        $pricing = Pricing::data();

        return response()
            ->view('admin.dashboard', compact(
                'totalBookings',
                'pendingCount',
                'confirmedCount',
                'todaysCount',
                'cancelledCount',
                'totalRevenue',
                'bookings',
                'courts',
                'openPlayUpcoming',
                'openPlayPast',
                'openPlayStats',
                'openPlayPastTotals',
                'pricing'
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

        // Store Walk-in Booking (one or many hourly slots, NO booking fee)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'contact_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]*$/'],
            'court_id'       => 'required|exists:courts,id',
            'booking_date'   => 'required|date',
            'slots'          => 'required|array|min:1',
            'slots.*'        => 'date_format:H:i',
            'payment_status' => 'required|string',
            'booking_status' => 'required|string',
        ]);

        $slots = collect($validated['slots'])->unique()->sort()->values();
        $reference = 'WK-' . strtoupper(Str::random(6));
        $total = $slots->count();

        $slotEnd = fn (string $slot) => ((int) substr($slot, 0, 2) === 23)
            ? '23:59'
            : Carbon::parse($slot)->addHour()->format('H:i');

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $slots, $reference, $total, $slotEnd) {
                // Lock the court so two admins/customers can't take the same slot at once
                Court::where('id', $validated['court_id'])->lockForUpdate()->firstOrFail();

                foreach ($slots as $slot) {
                    if (Booking::hasOverlap($validated['court_id'], $validated['booking_date'], $slot, $slotEnd($slot))) {
                        throw new \RuntimeException("The {$slot} slot is already booked for this court.");
                    }
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

                foreach ($slots as $index => $slot) {
                    // Price comes from Admin > Pricing, same as the public booking page
                    $rate = Booking::rateForHour((int) substr($slot, 0, 2));

                    Booking::create([
                        'booking_reference' => $total > 1 ? "{$reference}-" . ($index + 1) : $reference,
                        'customer_id'       => $customer->id,
                        'court_id'          => $validated['court_id'],
                        'customer_name'     => $validated['customer_name'],
                        'booking_date'      => $validated['booking_date'],
                        'start_time'        => $slot,
                        'end_time'          => $slotEnd($slot),
                        'duration'          => 1,
                        'total_price'       => $rate,
                        'total_amount'      => $rate,
                        'booking_fee'       => 0, // walk-ins never pay the online booking fee
                        'payment_method'    => 'Cash / Walk-in',
                        'payment_status'    => $validated['payment_status'],
                        'booking_status'    => $validated['booking_status'],
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

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