<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('open_play_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Open Play');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price_per_player', 8, 2);
            $table->unsignedSmallInteger('capacity');
            $table->string('format')->default('Traditional');   // Traditional or Smart Cycle (label only for now)
            $table->string('skill_level')->nullable();
            $table->text('inclusions')->nullable();
            $table->string('status')->default('Open');          // Open, Cancelled, Completed
            $table->timestamps();

            $table->index(['session_date', 'status']);
        });

        // Which courts each session uses (these courts are blocked from normal booking)
        Schema::create('court_open_play_session', function (Blueprint $table) {
            $table->foreignId('open_play_session_id')->constrained('open_play_sessions')->cascadeOnDelete();
            $table->foreignId('court_id')->constrained('courts')->cascadeOnDelete();
            $table->primary(['open_play_session_id', 'court_id']);
        });

        Schema::create('open_play_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_play_session_id')->constrained('open_play_sessions')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('reference')->unique();
            $table->string('name', 100);
            $table->string('contact_number', 30);
            $table->unsignedSmallInteger('players')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->string('payment_method', 20);
            $table->string('payment_status')->default('For Verification');
            $table->string('entry_status')->default('Pending Verification');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['open_play_session_id', 'entry_status']);
            $table->index('contact_number');
        });

        // Keep players' names and numbers off Supabase's public Data API (Laravel is not affected)
        if (DB::getDriverName() === 'pgsql') {
            foreach (['open_play_sessions', 'court_open_play_session', 'open_play_entries'] as $t) {
                DB::statement("ALTER TABLE {$t} ENABLE ROW LEVEL SECURITY");
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('open_play_entries');
        Schema::dropIfExists('court_open_play_session');
        Schema::dropIfExists('open_play_sessions');
    }
};