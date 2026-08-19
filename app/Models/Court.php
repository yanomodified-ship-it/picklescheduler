<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];

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

        // Get all non-cancelled bookings for TODAY
        $todayBookings = $this->bookings()
            ->whereDate('booking_date', today())
            ->where('booking_status', '!=', 'Cancelled')
            ->get();

        $bookedHours = 0;

        // Loop through today's bookings and add up the total hours
        foreach ($todayBookings as $booking) {
            $start = \Carbon\Carbon::parse($booking->start_time);
            $end = \Carbon\Carbon::parse($booking->end_time);
            
            $bookedHours += $start->diffInHours($end);
        }

        // Return true if the booked hours fill up the daily slots
        return $bookedHours >= $totalDailySlots;
    }
}