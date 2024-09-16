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
        Schema::create('scores', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->float('accuracy');
            $table->integer('max_combo');
            $table->string('mode');
            $table->json('mods')->default('[]');
            $table->json('statistics')->default('{}');
            $table->json('beatmap')->default('{}');
            $table->json('beatmapset')->default('{}');
            $table->boolean('passed');
            $table->boolean('perfect');
            $table->float('pp')->nullable();
            $table->string('rank');
            $table->unsignedBigInteger('score');
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
