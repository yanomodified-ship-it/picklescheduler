<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'classification',
        'price_per_hour',
        'operating_hours_start',
        'operating_hours_end',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if the court is fully booked for today.
     * Usage in Blade: $court->is_fully_booked
     */
    public function getIsFullyBookedAttribute(): bool
{
    // Your system has 17 hourly time slots per day (6:00 AM to 10:00 PM)
    $totalDailySlots = 17;

    // Only CONFIRMED bookings should count as taking up a slot.
    // "Pending" bookings are still awaiting payment verification and
    // must NOT mark the court as fully booked until an admin approves them.
    $activeStatuses = ['Confirmed'];

    if ($this->relationLoaded('bookings')) {
        // If the caller already eager-loaded today's confirmed bookings
        // (see BookingController::index()), reuse that instead of firing
        // a brand new query — this is what avoids the N+1 problem.
        $todayBookings = $this->bookings;
    } else {
        $todayBookings = $this->bookings()
            ->whereDate('booking_date', today())
            ->whereIn('booking_status', $activeStatuses)
            ->get();
    }

    $bookedHours = 0;

    foreach ($todayBookings as $booking) {
        $start = \Carbon\Carbon::parse($booking->start_time);
        $end = \Carbon\Carbon::parse($booking->end_time);

        $bookedHours += $start->diffInHours($end);
    }

    return $bookedHours >= $totalDailySlots;
}
}