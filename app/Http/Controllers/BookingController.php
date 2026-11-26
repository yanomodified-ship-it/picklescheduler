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
use App\Models\OpenPlaySession;
use App\Support\Pricing;

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

                // Next open play sessions for the homepage teaser. A problem here must never break the homepage.
        try {
            $openPlayTeasers = OpenPlaySession::with('courts')
                ->withSum(['entries as taken' => fn ($q) => $q->whereNotIn('entry_status', ['Rejected', 'Cancelled'])], 'players')
                ->where('status', 'Open')
                ->whereDate('session_date', '>=', now()->subDay()->format('Y-m-d'))
                ->whereDate('session_date', '<=', now()->addDays(90)->format('Y-m-d'))
                ->get()
                ->filter(fn ($s) => $s->endsAt()->gt(now()))
                ->sortBy(fn ($s) => $s->startsAt()->timestamp)
                ->take(3)
                ->values();
        } catch (\Throwable $e) {
            report($e);
            $openPlayTeasers = collect();
        }

        $pricing = Pricing::data();

        return view('welcome', compact('courts', 'openPlayTeasers', 'pricing'));
    }

    // Load the dedicated booking form page
    public function create(): View
    {
        $courts = Court::orderBy('id')->get();

        // Fetch CONFIRMED and PENDING bookings so the picker can grey out
        // confirmed slots and clearly flag pending ones (only cancelled/
        // rejected bookings should free up a slot again).
                $existingBookings = Booking::where('booking_date', '>=', now()->format('Y-m-d'))
            ->whereNotIn('booking_status', ['Cancelled', 'Rejected'])
            ->get(['court_id', 'booking_date', 'start_time', 'end_time', 'booking_status'])
            ->toBase();

        // Courts reserved for Open Play sessions show up as taken in the slot picker
        $openPlayBlocks = OpenPlaySession::with('courts')
            ->where('status', '!=', 'Cancelled')
            ->whereDate('session_date', '>=', now()->format('Y-m-d'))
            ->get()
            ->flatMap(fn ($s) => $s->courts->map(fn ($c) => [
                'court_id'       => $c->id,
                'booking_date'   => $s->session_date->format('Y-m-d'),
                'start_time'     => $s->start_time,
                'end_time'       => $s->end_time,
                'booking_status' => 'Confirmed',
            ]));

        $existingBookings = $existingBookings->concat($openPlayBlocks)->values();

        $pricing = Pricing::data();

        return view('booking', compact('courts', 'existingBookings', 'pricing'));
    }

    // Store a new booking: one or many courts, ONE booking fee for the whole booking
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_date'      => 'required|date|after_or_equal:today',
            'selections'        => 'required|array|min:1|max:80',
            'selections.*'      => ['required', 'string', 'regex:/^\d+\|([01]\d|2[0-3]):00$/'],
            'name'              => 'required|string|max:100',
            'contact_number'    => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'number_of_players' => 'required|integer|min:1|max:30',
            'payment_method'    => 'required|in:GCash,Maya',
            'payment_reference' => ['required', 'string', 'max:60'],
            'agree_terms'       => 'accepted',
        ], [
            'selections.required'          => 'Please select at least one time slot.',
            'selections.*.regex'           => 'One of the selected time slots is not valid. Please select your slots again.',
            'agree_terms.accepted'         => 'Please tick the box to agree to the booking terms and privacy policy.',
            'payment_reference.required'   => 'Please enter your GCash or Maya reference number.',
            'payment_reference.min'        => 'The reference number looks too short. Please copy it from your payment receipt.',
            'payment_reference.regex'      => 'The reference number can only contain letters, numbers, spaces and dashes.',
        ]);

        // "courtId|HH:MM" pairs, unique
        $pairs = collect($validated['selections'])
            ->unique()
            ->map(function (string $s) {
                [$courtId, $slot] = explode('|', $s, 2);
                return ['court_id' => (int) $courtId, 'slot' => $slot];
            })
            ->values();

        $courts = Court::whereIn('id', $pairs->pluck('court_id')->unique()->all())->get()->keyBy('id');
        $now = Carbon::now(config('app.timezone'));

        foreach ($pairs as $pair) {
            $court = $courts->get($pair['court_id']);
            $slot  = $pair['slot'];

            if (! $court) {
                return back()->withErrors(['time_slot' => 'One of the selected courts does not exist.'])->withInput();
            }
            if ($court->status !== 'active') {
                return back()->withErrors(['time_slot' => "{$court->name} is not currently available for booking."])->withInput();
            }

            // Operating hours: closing hour is INCLUSIVE (a court closing at 01:00 still offers the 1 AM - 2 AM slot)
            $startHour = (int) substr($court->operating_hours_start, 0, 2);
            $endHour   = (int) substr($court->operating_hours_end, 0, 2);
            if ($endHour < $startHour) {
                $endHour += 24; // runs past midnight
            }

            $slotHour = (int) substr($slot, 0, 2);
            $compare  = $slotHour < $startHour ? $slotHour + 24 : $slotHour; // after-midnight slots belong to the same booking day

            if ($compare < $startHour || $compare > $endHour) {
                return back()->withErrors(['time_slot' => "The {$slot} slot is outside {$court->name}'s operating hours."])->withInput();
            }

            // Reject slots that have already ended (Philippine time)
            $slotStart = Carbon::parse($validated['booking_date'] . ' ' . $slot, config('app.timezone'));
            if ($slotHour < $startHour) {
                $slotStart->addDay();
            }
            if ($slotStart->copy()->addHour()->lte($now)) {
                return back()->withErrors(['time_slot' => "The {$slot} slot has already passed. Please choose a later time."])->withInput();
            }
        }

        // Stable order: by court, then by time (after-midnight slots last)
        $pairs = $pairs->sortBy(function ($p) use ($courts) {
            $start = (int) substr($courts[$p['court_id']]->operating_hours_start, 0, 2);
            $h = (int) substr($p['slot'], 0, 2);
            return sprintf('%05d-%02d', $p['court_id'], $h < $start ? $h + 24 : $h);
        })->values();

        // One payment reference can only be used for ONE active booking
        if (strlen(str_replace([' ', '-'], '', $validated['payment_reference'])) >= 8 && Booking::whereRaw("REPLACE(REPLACE(LOWER(payment_reference), ' ', ''), '-', '') = ?", [strtolower(str_replace([' ', '-'], '', $validated['payment_reference']))])
            ->whereNotIn('booking_status', ['Rejected', 'Cancelled'])->exists()) {
            return back()
                ->withErrors(['payment_reference' => 'This reference number was already used for another booking. Please check it, or message us on Facebook.'])
                ->withInput();
        }

        $customer = Customer::firstOrCreate(
            ['contact_number' => $validated['contact_number']],
            [
                'full_name' => $validated['name'],
                'email'     => $validated['email'] ?? null,
            ]
        );

        $baseReference = $this->generateBookingReference();
        $bookingFee    = Pricing::fee();
        $totalRows     = $pairs->count();

        try {
            DB::transaction(function () use ($pairs, $courts, $validated, $customer, $baseReference, $bookingFee, $totalRows) {

                // Lock every involved court (always in id order, so two bookings can't deadlock each other)
                Court::whereIn('id', $courts->keys()->all())->orderBy('id')->lockForUpdate()->get();

                foreach ($pairs as $pair) {
                    if (Booking::hasOverlap($pair['court_id'], $validated['booking_date'], $pair['slot'], $this->slotEnd($pair['slot']))) {
                        $name = $courts[$pair['court_id']]->name;
                        throw new \RuntimeException("The {$pair['slot']} slot on {$name} is already booked. Please choose another time or court.");
                    }
                }

                foreach ($pairs as $index => $pair) {
                    $rate = Booking::rateForHour((int) substr($pair['slot'], 0, 2));

                    Booking::create([
                        'booking_reference' => $totalRows > 1 ? "{$baseReference}-" . ($index + 1) : $baseReference,
                        'customer_id'       => $customer->id,
                        'customer_name'     => $validated['name'],
                        'court_id'          => $pair['court_id'],
                        'booking_date'      => $validated['booking_date'],
                        'start_time'        => $pair['slot'],
                        'end_time'          => $this->slotEnd($pair['slot']),
                        'number_of_players' => $validated['number_of_players'] ?? 2,
                        'duration'          => 1,
                        'total_price'       => $rate,
                        'total_amount'      => $rate,
                        // ONE flat fee per booking reference, no matter how many courts or hours
                        'booking_fee'       => $index === 0 ? $bookingFee : 0,
                        'booking_status'    => 'Pending Verification',
                        'payment_status'    => 'For Verification',
                        'payment_method'    => $validated['payment_method'],
                        'payment_reference' => trim($validated['payment_reference']),
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['time_slot' => $e->getMessage()])->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);

            return back()
                ->withErrors(['time_slot' => 'That slot was just booked by someone else, or something went wrong. Please try again.'])
                ->withInput();
        }

        return redirect()->route('bookings.confirmation', ['booking_reference' => $baseReference])
            ->with('success', "Booking submitted successfully!");
    }

    /** End time of a one-hour slot. 23:00 ends at 23:59 (a time column cannot hold 24:00). */
    private function slotEnd(string $slot): string
    {
        return ((int) substr($slot, 0, 2) === 23)
            ? '23:59'
            : Carbon::parse($slot)->addHour()->format('H:i');
    }

    /**
     * Display booking confirmation page.
     */
    public function confirmation(string $booking_reference): View
    {
                // Only accept a full, well-formed reference (blocks "PKL-" or "P" wildcard guessing)
        if (! preg_match('/^PKL-[A-Z0-9]{6}$/', $booking_reference)) {
            abort(404);
        }

        // The booking itself (PKL-4F8A21) plus its multi-slot siblings (PKL-4F8A21-1, -2, ...)
        $bookings = Booking::query()
            ->with(['court', 'customer'])
            ->where(function ($q) use ($booking_reference) {
                $q->where('booking_reference', $booking_reference)
                  ->orWhere('booking_reference', 'LIKE', $booking_reference . '-%');
            })
            ->orderBy('court_id')
            ->orderByRaw("CASE WHEN start_time < '04:00:00' THEN 1 ELSE 0 END, start_time")
            ->get();

        if ($bookings->isEmpty()) {
            abort(404);
        }

        $booking = $bookings->first();
        $booking->booking_reference = $booking_reference; // Retain base reference for clean UI display
        $bookingFee  = (float) $bookings->sum('booking_fee');
        $hoursTotal  = $bookings->sum(fn ($b) => (float) ($b->total_price ?? $b->total_amount ?? 0));
        $totalAmount = $hoursTotal + $bookingFee; // what the customer actually has to send

        return view('confirmation', compact('booking', 'bookings', 'totalAmount', 'bookingFee', 'hoursTotal'));
    }

    /**
     * Generate a unique booking reference such as PKL-4F8A21.
     */
    private function generateBookingReference(): string
    {
        do {
                        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no 0, O, 1 or I
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $reference = 'PKL-' . $code;

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