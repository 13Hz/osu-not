<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_user_filter', function (Blueprint $table) {
            $table->boolean('active')->default(false);
            $table->unsignedBigInteger('chat_user_id');
            $table->foreign('chat_user_id')->references('id')->on('chat_user');
            $table->unsignedBigInteger('filter_id');
            $table->foreign('filter_id')->references('id')->on('filters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats_users_filters');
    }
};
