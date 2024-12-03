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
        Schema::table('scores', function (Blueprint $table) {
            $table->unsignedBigInteger('beatmapset_id')->after('mods');
            $table->unsignedBigInteger('beatmap_id')->after('beatmapset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn('beatmapset_id');
            $table->dropColumn('beatmap_id');
        });
    }
};
