<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpenPlaySession extends Model
{
    protected $fillable = [
        'title', 'session_date', 'start_time', 'end_time', 'price_per_player',
        'capacity', 'format', 'skill_level', 'inclusions', 'status',
    ];

    protected $casts = [
        'session_date'     => 'date',
        'price_per_player' => 'decimal:2',
        'capacity'         => 'integer',
    ];

    public function courts(): BelongsToMany
    {
        return $this->belongsToMany(Court::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(OpenPlayEntry::class);
    }

    /** Entries that still hold a spot (rejected and cancelled ones free it up). */
    public function activeEntries(): HasMany
    {
        return $this->entries()->whereNotIn('entry_status', ['Rejected', 'Cancelled']);
    }

    /** Sessions that are past or cancelled (the same rule the admin lists use). */
public static function pastQuery()
{
    $today = now()->format('Y-m-d');

    return self::where(fn ($q) => $q->where('status', '!=', 'Open')->orWhereDate('session_date', '<', $today));
}

/** Numbers shown in the "Delete all" confirmation. */
public static function pastTotals(): array
{
    $sessions = self::pastQuery()->get(['id', 'status']);

    $entries = OpenPlayEntry::whereIn('open_play_session_id', $sessions->pluck('id'))->count();

    // Revenue that disappears from the totals: verified entries on past sessions that were not cancelled
    $revenue = (float) OpenPlayEntry::whereIn('open_play_session_id', $sessions->where('status', '!=', 'Cancelled')->pluck('id'))
        ->where('entry_status', 'Confirmed')
        ->where('payment_status', 'Verified')
        ->sum('total_price');

    return ['sessions' => $sessions->count(), 'entries' => $entries, 'revenue' => $revenue];
}

    /** Upcoming and past/cancelled sessions, with players taken, for the admin dashboard. */
public static function adminLists(): array
{
    $today = now()->format('Y-m-d');

        $all = self::with(['courts', 'entries' => fn ($q) => $q->orderBy('created_at')])
        ->withSum(['entries as taken' => fn ($q) => $q->whereNotIn('entry_status', ['Rejected', 'Cancelled'])], 'players')
        ->orderBy('session_date')
        ->orderBy('start_time')
        ->get();

    $isUpcoming = fn ($s) => $s->status === 'Open' && $s->session_date->format('Y-m-d') >= $today;

    return [
        $all->filter($isUpcoming)->values(),
        $all->reject($isUpcoming)->sortByDesc('session_date')->take(15)->values(),
    ];
}

    /**
 * Is this court taken by an open play session during the given time?
 */
public static function blocks($courtId, $date, $startTime, $endTime, $ignoreSessionId = null): bool
{
    return self::where('status', '!=', 'Cancelled')
        ->whereDate('session_date', $date)
        ->whereHas('courts', fn ($q) => $q->where('courts.id', $courtId))
        ->where('start_time', '<', $endTime)
        ->where('end_time', '>', $startTime)
        ->when($ignoreSessionId, fn ($q) => $q->where('id', '!=', $ignoreSessionId))
        ->exists();
}

    /** When the session starts. Hours before opening (e.g. 12 AM) belong to the night of session_date, so they fall on the next day. */
public function startsAt(): \Carbon\Carbon
{
    $start = \Carbon\Carbon::parse($this->session_date->format('Y-m-d') . ' ' . $this->start_time);

    if ($start->hour < 4) {
        $start->addDay();
    }

    return $start;
}

public function endsAt(): \Carbon\Carbon
{
    $start   = $this->startsAt();
    $endTime = substr($this->end_time, 0, 5);
    $end     = \Carbon\Carbon::parse($this->session_date->format('Y-m-d') . ' ' . $endTime);

    if ($endTime === '23:59') {
        $end->addMinute();   // 23:59 is stored for "until midnight"
    }

    while ($end->lte($start)) {
        $end->addDay();
    }

    return $end;
}

/** Players who hold a spot. Uses the withSum() value when it was loaded, to avoid an extra query. */
public function getJoinedAttribute(): int
{
    return array_key_exists('taken', $this->attributes)
        ? (int) $this->attributes['taken']
        : $this->spots_taken;
}

public function getLeftAttribute(): int
{
    return max(0, $this->capacity - $this->joined);
}

/** cancelled | ended | full | live | open */
public function getStateAttribute(): string
{
    if ($this->status === 'Cancelled') {
        return 'cancelled';
    }
    if ($this->endsAt()->lte(now())) {
        return 'ended';
    }
    if ($this->left <= 0) {
        return 'full';
    }

    return $this->startsAt()->lte(now()) ? 'live' : 'open';
}

    public function getSpotsTakenAttribute(): int
    {
        return (int) $this->activeEntries()->sum('players');
    }

    public function getSpotsLeftAttribute(): int
    {
        return max(0, $this->capacity - $this->spots_taken);
    }
}