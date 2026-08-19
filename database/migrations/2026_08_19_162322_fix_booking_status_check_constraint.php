<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop the old check constraint
        DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_booking_status_check");

        // 2. Normalize existing rows with invalid or NULL statuses
        DB::statement("UPDATE bookings SET booking_status = 'Pending Verification' WHERE booking_status IS NULL OR booking_status NOT IN ('Pending Verification', 'Confirmed', 'Cancelled', 'Rejected', 'pending', 'confirmed', 'cancelled', 'rejected')");

        // 3. Apply the updated constraint
        DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_booking_status_check CHECK (booking_status IN ('Pending Verification', 'Confirmed', 'Cancelled', 'Rejected', 'pending', 'confirmed', 'cancelled', 'rejected'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_booking_status_check");
    }
};