<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('court_id')->constrained('courts')->onDelete('cascade');
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration'); // in hours or minutes
            $table->decimal('total_amount', 10, 2);
            $table->enum('booking_status', [
                'pending_verification',
                'confirmed',
                'rejected',
                'cancelled',
                'completed'
            ])->default('pending_verification');
            $table->timestamps();

            // Indexes to optimize query performance for double-booking checks
            $table->index(['court_id', 'booking_date']);
            $table->index(['booking_date', 'start_time', 'end_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};