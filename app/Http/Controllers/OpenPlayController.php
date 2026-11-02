<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OpenPlayEntry;
use App\Models\OpenPlaySession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\SkillLevels;
use Illuminate\Validation\Rule;

class OpenPlayController extends Controller
{
    private const MAX_PLAYERS_PER_ENTRY = 10;
    private const MAX_ENTRIES_PER_PHONE = 3;

    public function index(): View
    {
        $now = now();

            $sessions = OpenPlaySession::with(['courts', 'entries' => fn ($q) => $q->where('entry_status', 'Confirmed')->orderBy('created_at')])
            ->withSum(['entries as pending_players' => fn ($q) => $q->where('entry_status', 'Pending Verification')], 'players')
            ->withSum(['entries as taken' => fn ($q) => $q->whereNotIn('entry_status', ['Rejected', 'Cancelled'])], 'players')
            ->where('status', 'Open')
            ->whereDate('session_date', '>=', $now->copy()->subDay()->format('Y-m-d'))
            ->whereDate('session_date', '<=', $now->copy()->addDays(90)->format('Y-m-d'))
            ->get()
            ->sortBy(fn ($s) => $s->startsAt()->timestamp)
            ->values();

        $endOfToday = $now->copy()->endOfDay();

        // Today: starts today, or started earlier and is still running
        $today = $sessions->filter(fn ($s) =>
            $s->startsAt()->lte($endOfToday)
            && ($s->startsAt()->isSameDay($now) || $s->endsAt()->gt($now))
        )->values();

        $upcoming = $sessions->filter(fn ($s) => $s->startsAt()->gt($endOfToday))->values();

        $todayHasOpen = $today->contains(fn ($s) => $s->state !== 'ended');
        $defaultTab   = (! $todayHasOpen && $upcoming->isNotEmpty()) ? 'upcoming' : 'today';

        return view('open-play', compact('today', 'upcoming', 'defaultTab'));
    }

            public function show(OpenPlaySession $openPlaySession): RedirectResponse
    {
        // The roster is now a pop-up on the sessions list
        return redirect()->route('open-play.index');
    }

    public function join(OpenPlaySession $openPlaySession): View
    {
        $session = OpenPlaySession::with('courts')
            ->withSum(['entries as taken' => fn ($q) => $q->whereNotIn('entry_status', ['Rejected', 'Cancelled'])], 'players')
            ->findOrFail($openPlaySession->id);

        $state      = $session->state;
        $canJoin    = in_array($state, ['open', 'live'], true);
        $maxPlayers = max(1, min(self::MAX_PLAYERS_PER_ENTRY, $session->left));
        $blocked    = match ($state) {
            'full'      => 'This session is full. Check the other sessions, or book a court for your own group.',
            'ended'     => 'This session has already ended.',
            'cancelled' => 'This session was cancelled.',
            default     => null,
        };

        return view('open-play-join', compact('session', 'canJoin', 'maxPlayers', 'blocked'));
    }

    public function store(Request $request, OpenPlaySession $openPlaySession): RedirectResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'contact_number' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'players'        => 'required|integer|min:1|max:' . self::MAX_PLAYERS_PER_ENTRY,
            'payment_method' => 'required|in:GCash,Maya',
            'skill_level'    => ['required', Rule::in(SkillLevels::names())],
            'hide_name'      => 'nullable|boolean',
            'note'           => 'nullable|string|max:255',
            'agree_terms'    => 'accepted',
        ], [
            'agree_terms.accepted' => 'Please tick the box to confirm you understand the joining terms.',
            'skill_level.required' => 'Please pick your skill level.',
            'skill_level.in'       => 'Please pick one of the listed skill levels.',
        ]);

        $phone = preg_replace('/[^0-9+]/', '', $data['contact_number']);

        try {
            $entry = DB::transaction(function () use ($openPlaySession, $data, $phone) {
                // Lock the session row so two people can never take the last spot together
                $session = OpenPlaySession::whereKey($openPlaySession->id)->lockForUpdate()->firstOrFail();

                if ($session->status !== 'Open' || $session->endsAt()->lte(now())) {
                    throw new \RuntimeException('Sorry, this session is no longer open for joining.');
                }

                $active = $session->entries()->whereNotIn('entry_status', ['Rejected', 'Cancelled']);

                $taken = (int) (clone $active)->sum('players');
                $left  = max(0, $session->capacity - $taken);

                if ($left <= 0) {
                    throw new \RuntimeException('Sorry, this session just became full.');
                }
                if ($data['players'] > $left) {
                    throw new \RuntimeException("Only {$left} spot(s) left in this session. Please lower the number of players.");
                }

                if ((clone $active)->where('contact_number', $phone)->count() >= self::MAX_ENTRIES_PER_PHONE) {
                    throw new \RuntimeException('This contact number already has the maximum number of entries for this session. Please message us on Facebook if you need more.');
                }

                $customer = Customer::firstOrCreate(
                    ['contact_number' => $data['contact_number']],
                    ['full_name' => $data['name'], 'email' => null]
                );

                return OpenPlayEntry::create([
                    'open_play_session_id' => $session->id,
                    'customer_id'          => $customer->id,
                    'reference'            => $this->makeReference(),
                    'name'                 => $data['name'],
                    'contact_number'       => $phone,
                    'players'              => $data['players'],
                    'skill_level'          => $data['skill_level'],
                    'hide_name'            => (bool) ($data['hide_name'] ?? false),
                    'note'                 => isset($data['note']) ? trim($data['note']) : null,
                    'total_price'          => $data['players'] * (float) $session->price_per_player,
                    'payment_method'       => $data['payment_method'],
                    'payment_status'       => 'For Verification',
                    'entry_status'         => 'Pending Verification',
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['players' => $e->getMessage()])->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);

            return back()
                ->withErrors(['players' => 'Something went wrong while saving your spot. Please try again.'])
                ->withInput();
        }

        return redirect()->route('open-play.confirmation', ['reference' => $entry->reference]);
    }

    public function confirmation(string $reference): View
    {
        if (! preg_match('/^OPL-[A-HJ-NP-Z2-9]{6}$/', $reference)) {
            abort(404);
        }

        $entry = OpenPlayEntry::with('session.courts')->where('reference', $reference)->firstOrFail();

        return view('open-play-confirmation', compact('entry'));
    }

    private function makeReference(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';   // no 0, O, 1 or I

        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $reference = 'OPL-' . $code;
        } while (OpenPlayEntry::where('reference', $reference)->exists());

        return $reference;
    }
}