<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->default('GCash')->after('number_of_players');
            }
        });

        Schema::table('courts', function (Blueprint $table) {
            if (!Schema::hasColumn('courts', 'status')) {
                $table->string('status')->default('active')->after('description'); // active, maintenance, disabled
            }
            if (!Schema::hasColumn('courts', 'price_per_hour')) {
                $table->decimal('price_per_hour', 8, 2)->default(500.00)->after('status');
            }
            if (!Schema::hasColumn('courts', 'operating_hours_start')) {
                $table->time('operating_hours_start')->default('06:00:00')->after('price_per_hour');
            }
            if (!Schema::hasColumn('courts', 'operating_hours_end')) {
                $table->time('operating_hours_end')->default('22:00:00')->after('operating_hours_start');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {$table->dropColumn(['rejection_reason', 'payment_method']);
        });

        Schema::table('courts', function (Blueprint $table) {$table->dropColumn(['status', 'price_per_hour', 'operating_hours_start', 'operating_hours_end']);
        });
    }
};