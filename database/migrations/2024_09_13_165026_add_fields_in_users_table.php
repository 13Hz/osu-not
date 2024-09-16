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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_score_hash');
            $table->unsignedBigInteger('last_score_id')->nullable();
            $table->foreign('last_score_id')->references('id')->on('scores')->onDelete('set null');
            $table->string('avatar_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['last_score_id']);
            $table->dropColumn([
                'last_score_id',
                'avatar_url'
            ]);
            $table->string('last_score_hash')->nullable();
        });
    }
};
