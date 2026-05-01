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
        Schema::create('vocabulary_favorites', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->foreignId('word_id')->constrained('vocabulary_words')->onDelete('cascade');
            $table->uuid('word_uid');
            $table->foreign('word_uid')->references('uid')->on('vocabulary_words')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_uid', 'word_uid'], 'unique_favorite');
            $table->index('user_uid');
            $table->index('word_uid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabulary_favorites');
    }
};
