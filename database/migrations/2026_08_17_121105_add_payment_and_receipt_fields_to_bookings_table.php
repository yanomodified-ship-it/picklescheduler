<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add payment, receipt, reference, and lifecycle status fields.
     *
     * This migration checks for existing columns first so it is safer
     * when some of these fields already exist in the current schema.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            if (! Schema::hasColumn('bookings', 'booking_reference')) {
                $table->string('booking_reference', 20)
                    ->nullable()
                    ->unique();
            }

            if (! Schema::hasColumn('bookings', 'payment_reference')) {
                $table->string('payment_reference', 100)
                    ->nullable();
            }

            if (! Schema::hasColumn('bookings', 'receipt_path')) {
                $table->string('receipt_path', 500)
                    ->nullable();
            }

            if (! Schema::hasColumn('bookings', 'booking_status')) {
                $table->string('booking_status', 30)
                    ->default('Pending Verification');
            }

            if (! Schema::hasColumn('bookings', 'payment_status')) {
                $table->string('payment_status', 30)
                    ->default('For Verification');
            }
        });

        /*
         * Add indexes separately so this migration remains compatible
         * with PostgreSQL/Supabase and existing indexes.
         */
        Schema::table('bookings', function (Blueprint $table): void {
            if (! Schema::hasColumn('bookings', 'payment_reference')) {
                return;
            }

            $table->index('payment_status', 'bookings_payment_status_index');
            $table->index('booking_status', 'bookings_booking_status_index');
            $table->index('booking_date', 'bookings_booking_date_index');
            $table->index(
                ['court_id', 'booking_date'],
                'bookings_court_date_index'
            );
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $indexes = [
                'bookings_payment_status_index',
                'bookings_booking_status_index',
                'bookings_booking_date_index',
                'bookings_court_date_index',
            ];

            foreach ($indexes as $index) {
                try {
                    $table->dropIndex($index);
                } catch (\Throwable) {
                    // Index may already have been removed.
                }
            }

            if (Schema::hasColumn('bookings', 'receipt_path')) {
                $table->dropColumn('receipt_path');
            }

            if (Schema::hasColumn('bookings', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }

            if (Schema::hasColumn('bookings', 'booking_status')) {
                $table->dropColumn('booking_status');
            }

            if (Schema::hasColumn('bookings', 'payment_status')) {
                $table->dropColumn('payment_status');
            }

            if (Schema::hasColumn('bookings', 'booking_reference')) {
                $table->dropUnique('bookings_booking_reference_unique');
                $table->dropColumn('booking_reference');
            }
        });
    }
};