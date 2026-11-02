<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('open_play_entries', function (Blueprint $table) {
            $table->string('skill_level', 20)->nullable()->after('players');
            $table->boolean('hide_name')->default(false)->after('skill_level');
            $table->string('note', 255)->nullable()->after('hide_name');
        });
    }

    public function down(): void
    {
        Schema::table('open_play_entries', function (Blueprint $table) {
            $table->dropColumn(['skill_level', 'hide_name', 'note']);
        });
    }
};