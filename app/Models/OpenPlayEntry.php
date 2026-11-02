<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenPlayEntry extends Model
{
    protected $fillable = [
        'open_play_session_id', 'customer_id', 'reference', 'name', 'contact_number',
        'players', 'skill_level', 'hide_name', 'note', 'total_price', 'payment_method',
        'payment_status', 'entry_status', 'rejection_reason',
    ];

    protected $casts = [
        'players'     => 'integer',
        'hide_name'   => 'boolean',
        'total_price' => 'decimal:2',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(OpenPlaySession::class, 'open_play_session_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** Open play revenue for the admin tab. Court bookings are not included. */
public static function revenueStats(): array
{
    $live = fn ($q) => $q->where('status', '!=', 'Cancelled');

    $collected = fn () => self::where('entry_status', 'Confirmed')
        ->where('payment_status', 'Verified')
        ->whereHas('session', $live);

    $today      = now()->format('Y-m-d');
    $monthStart = now()->startOfMonth()->format('Y-m-d');
    $monthEnd   = now()->endOfMonth()->format('Y-m-d');

    return [
        'today'   => (float) $collected()->whereHas('session', fn ($q) => $q->whereDate('session_date', $today))->sum('total_price'),
        'month'   => (float) $collected()->whereHas('session', fn ($q) => $q->whereBetween('session_date', [$monthStart, $monthEnd]))->sum('total_price'),
        'all'     => (float) $collected()->sum('total_price'),
        'players' => (int) $collected()->sum('players'),
        'pending' => (float) self::where('entry_status', 'Pending Verification')->whereHas('session', $live)->sum('total_price'),
    ];
}

    /** Name shown on the public roster: "Juan D." or "Player" when the person opted out. Never the phone number. */
    public function getPublicNameAttribute(): string
    {
        if ($this->hide_name) {
            return 'Player';
        }

        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];

        if (count($parts) < 2) {
            return $parts[0] ?? 'Player';
        }

        return $parts[0] . ' ' . mb_strtoupper(mb_substr(end($parts), 0, 1)) . '.';
    }
}