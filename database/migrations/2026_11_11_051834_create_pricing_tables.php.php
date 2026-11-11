<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pricing_rules')) {
            Schema::create('pricing_rules', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                // "Service hours": 0-23 = same day, 24 = 12 AM, 25 = 1 AM, 26 = 2 AM (next day)
                $table->unsignedTinyInteger('start_hour');
                $table->unsignedTinyInteger('end_hour'); // exclusive
                $table->decimal('rate', 10, 2);
                $table->timestamps();
            });
        }

        // Named "pricing_settings" because a "settings" table already exists in your database
        if (! Schema::hasTable('pricing_settings')) {
            Schema::create('pricing_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('bookings', 'booking_fee')) {
            Schema::table('bookings', function (Blueprint $table) {
                // Flat per-reservation fee, stored on ONE row of a multi-slot booking
                $table->decimal('booking_fee', 10, 2)->default(0);
            });
        }

        $now = now();

        if (DB::table('pricing_rules')->count() === 0) {
            DB::table('pricing_rules')->insert([
                ['label' => 'Morning',   'start_hour' => 4,  'end_hour' => 12, 'rate' => 250, 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'Afternoon', 'start_hour' => 12, 'end_hour' => 17, 'rate' => 300, 'created_at' => $now, 'updated_at' => $now],
                ['label' => 'Evening',   'start_hour' => 17, 'end_hour' => 26, 'rate' => 350, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('pricing_settings')->count() === 0) {
            DB::table('pricing_settings')->insert([
                ['key' => 'open_hour',   'value' => '4',  'created_at' => $now, 'updated_at' => $now],
                ['key' => 'close_hour',  'value' => '2',  'created_at' => $now, 'updated_at' => $now],
                ['key' => 'booking_fee', 'value' => '20', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        // Courts now run 4:00 AM -> 2:00 AM (last slot starts at 1 AM)
        DB::table('courts')->update([
            'operating_hours_start' => '04:00',
            'operating_hours_end'   => '01:00',
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('bookings', 'booking_fee')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('booking_fee');
            });
        }
        Schema::dropIfExists('pricing_settings');
        Schema::dropIfExists('pricing_rules');
    }
};