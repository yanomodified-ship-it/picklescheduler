<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\OpenPlaySession;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OpenPlayEntry;

class AdminOpenPlayController extends Controller
{
    /** The old standalone address now just opens the dashboard's Open Play tab. */
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.dashboard', ['tab' => 'openplay']);
    }

    public function store(Request $request): RedirectResponse
    {
                // "12:00 AM" as an end time means "until midnight"
        if ($request->input('end_time') === '00:00') {
            $request->merge(['end_time' => '23:59']);
        }
        $data = $request->validateWithBag('openplay', [
            'title'            => 'required|string|max:120',
            'session_date'     => 'required|date|after_or_equal:today',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'price_per_player' => 'required|numeric|min:0|max:100000',
            'capacity'         => 'required|integer|min:1|max:500',
            'format'           => 'required|in:Traditional,Smart Cycle',
            'skill_level'      => 'nullable|string|max:150',
            'inclusions'       => 'nullable|string|max:1000',
            'courts'           => 'required|array|min:1',
            'courts.*'         => 'integer|exists:courts,id',
        ], [
            'courts.required' => 'Please choose at least one court for this session.',
            'end_time.after'  => 'The end time must be after the start time. A session cannot run past midnight; make a second session starting at 12:00 AM instead.',
        ]);

        $courts = Court::whereIn('id', $data['courts'])->orderBy('id')->get();

        foreach ($courts as $court) {
            if (! $this->withinHours($data['start_time'], $data['end_time'], $court)) {
                return back()
                    ->withErrors(['start_time' => "{$court->name} is not open for that whole time range."], 'openplay')
                    ->withInput();
            }
        }

        try {
            DB::transaction(function () use ($data) {
                // Same locking as customer bookings, so the two can never collide
                Court::whereIn('id', $data['courts'])->orderBy('id')->lockForUpdate()->get();

                $conflicts = $this->conflicts($data);
                if (! empty($conflicts)) {
                    throw new \RuntimeException(implode(' ', $conflicts));
                }

                $session = OpenPlaySession::create([
                    'title'            => $data['title'],
                    'session_date'     => $data['session_date'],
                    'start_time'       => $data['start_time'],
                    'end_time'         => $data['end_time'],
                    'price_per_player' => $data['price_per_player'],
                    'capacity'         => $data['capacity'],
                    'format'           => $data['format'],
                    'skill_level'      => $data['skill_level'] ?? null,
                    'inclusions'       => $data['inclusions'] ?? null,
                    'status'           => 'Open',
                ]);

                $session->courts()->sync($data['courts']);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['courts' => $e->getMessage()], 'openplay')->withInput();
        }

        return redirect()->route('admin.dashboard')
            ->with('openplay_msg', 'Open play session created. Its courts are now blocked from normal booking during that time.');
    }

    public function cancel(OpenPlaySession $openPlaySession): RedirectResponse
    
    {
        $openPlaySession->update(['status' => 'Cancelled']);

        return redirect()->route('admin.dashboard')
            ->with('openplay_msg', 'Session cancelled. Its courts are open for booking again.');
    }

        public function deletePast(Request $request): RedirectResponse
    {
        $request->validateWithBag('openplay', [
            'confirm' => ['required', 'in:DELETE ALL'],
        ], [
            'confirm.required' => 'Type DELETE ALL to confirm.',
            'confirm.in'       => 'Type DELETE ALL exactly (capital letters) to confirm.',
        ]);

        // Players and court links are removed by the database along with each session
        $count = DB::transaction(fn () => OpenPlaySession::pastQuery()->delete());

        return redirect()->route('admin.dashboard')
            ->with('openplay_msg', "Deleted {$count} past and cancelled session(s) together with their players. Upcoming sessions were not touched.");
    }

        public function verifyEntry(OpenPlayEntry $openPlayEntry): RedirectResponse
    {
        if ($openPlayEntry->entry_status !== 'Pending Verification') {
            return redirect()->route('admin.dashboard')
                ->with('openplay_msg', "{$openPlayEntry->reference} is already {$openPlayEntry->entry_status}, so nothing was changed.");
        }

        $openPlayEntry->update([
            'payment_status'   => 'Verified',
            'entry_status'     => 'Confirmed',
            'rejection_reason' => null,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('openplay_msg', "Verified {$openPlayEntry->name} ({$openPlayEntry->reference}). Their confirmation page now says they are in.");
    }

    public function rejectEntry(Request $request, OpenPlayEntry $openPlayEntry): RedirectResponse
    {
        $data = $request->validateWithBag('openplay', [
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        if (in_array($openPlayEntry->entry_status, ['Rejected', 'Cancelled'], true)) {
            return redirect()->route('admin.dashboard')
                ->with('openplay_msg', "{$openPlayEntry->reference} is already {$openPlayEntry->entry_status}, so nothing was changed.");
        }

        $openPlayEntry->update([
            'payment_status'   => 'Rejected',
            'entry_status'     => 'Rejected',
            'rejection_reason' => $data['rejection_reason'] ?? null,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('openplay_msg', "Rejected {$openPlayEntry->name} ({$openPlayEntry->reference}). Their spot(s) are free again.");
    }

    public function cancelEntry(OpenPlayEntry $openPlayEntry): RedirectResponse
    {
        if (in_array($openPlayEntry->entry_status, ['Rejected', 'Cancelled'], true)) {
            return redirect()->route('admin.dashboard')
                ->with('openplay_msg', "{$openPlayEntry->reference} is already {$openPlayEntry->entry_status}, so nothing was changed.");
        }

        $openPlayEntry->update(['entry_status' => 'Cancelled']);

        return redirect()->route('admin.dashboard')
            ->with('openplay_msg', "Cancelled {$openPlayEntry->name} ({$openPlayEntry->reference}). Their spot(s) are free again. If they already paid, any refund is up to you.");
    }

    /** Existing bookings or other sessions that would clash with the new session. */
    private function conflicts(array $d): array
    {
        $messages = [];

        $bookings = Booking::with('court')
            ->whereIn('court_id', $d['courts'])
            ->where('booking_date', $d['session_date'])
            ->whereNotIn('booking_status', ['Rejected', 'Cancelled'])
            ->where('start_time', '<', $d['end_time'])
            ->where('end_time', '>', $d['start_time'])
            ->get();

        foreach ($bookings as $b) {
            $messages[] = ($b->court->name ?? 'A court') . ' already has booking ' . $b->booking_reference
                . ' at ' . Carbon::parse($b->start_time)->format('g:i A') . '.';
        }

        foreach ($d['courts'] as $courtId) {
            if (OpenPlaySession::blocks($courtId, $d['session_date'], $d['start_time'], $d['end_time'])) {
                $name = Court::find($courtId)->name ?? 'A court';
                $messages[] = "{$name} is already used by another open play session at that time.";
            }
        }

        if (count($messages) > 5) {
            $extra      = count($messages) - 5;
            $messages   = array_slice($messages, 0, 5);
            $messages[] = "...and {$extra} more.";
        }

        return $messages;
    }

    /** Every hour of the session must fall inside the court's operating hours. */
    private function withinHours(string $start, string $end, Court $court): bool
    {
        $open  = (int) substr($court->operating_hours_start, 0, 2);
        $close = (int) substr($court->operating_hours_end, 0, 2);   // last bookable hour (inclusive)

        $ok = fn (int $h) => $close < $open
            ? ($h >= $open || $h <= $close)
            : ($h >= $open && $h <= $close);

        $firstHour = (int) substr($start, 0, 2);
        $lastHour  = ((int) substr($end, 3, 2) === 0) ? (int) substr($end, 0, 2) - 1 : (int) substr($end, 0, 2);

        for ($h = $firstHour; $h <= $lastHour; $h++) {
            if (! $ok($h)) {
                return false;
            }
        }

        return true;
    }
}