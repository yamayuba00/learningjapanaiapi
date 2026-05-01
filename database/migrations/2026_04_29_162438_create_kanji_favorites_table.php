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
        Schema::create('kanji_favorites', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->foreignId('kanji_id')->constrained('kanji')->onDelete('cascade');
            $table->uuid('kanji_uid');
            $table->foreign('kanji_uid')->references('uid')->on('kanji')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_uid', 'kanji_uid'], 'unique_favorite');
            $table->index('user_uid');
            $table->index('kanji_uid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanji_favorites');
    }
};
