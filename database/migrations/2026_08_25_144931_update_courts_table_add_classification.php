<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::table('courts', function (Blueprint $table) {
        $table->dropColumn('price_per_hour'); // Remove the rate
        $table->string('classification')->default('Indoor')->after('status'); // Add classification
    });
}

public function down(): void
{
    Schema::table('courts', function (Blueprint $table) {
        $table->decimal('price_per_hour', 8, 2)->default(0);
        $table->dropColumn('classification');
    });
}
};
