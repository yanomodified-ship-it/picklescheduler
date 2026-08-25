<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
    'booking_reference',
    'customer_id',
    'court_id',
    'booking_date',
    'start_time',
    'end_time',
    'number_of_players',
    'duration',
    'total_price', 
    'total_amount',  
    'booking_status',
    'payment_status',
    'payment_method',
];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    /**
     * Prevents double bookings by checking for overlapping time slots.
     * Overlap condition: (StartA < EndB) AND (EndA > StartB)
     */
    public static function hasOverlap($courtId, $date, $startTime, $endTime, $ignoreBookingId = null): bool
    {
        return self::where('court_id', $courtId)
            ->where('booking_date', $date)
            ->whereNotIn('booking_status', ['rejected', 'cancelled'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })
            ->when($ignoreBookingId, function ($query) use ($ignoreBookingId) {
                $query->where('id', '!=', $ignoreBookingId);
            })
            ->exists();
    }
}