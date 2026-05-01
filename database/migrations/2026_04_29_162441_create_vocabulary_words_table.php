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
        Schema::create('vocabulary_words', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('category_id')->constrained('vocabulary_categories')->onDelete('cascade');
            $table->uuid('category_uid');
            $table->foreign('category_uid')->references('uid')->on('vocabulary_categories')->onDelete('cascade');
            $table->string('japanese', 100);
            $table->string('romaji', 100);
            $table->string('indonesian', 100);
            $table->enum('level', ['N5', 'N4', 'N3', 'N2', 'N1'])->nullable();
            $table->text('example_sentence_jp')->nullable();
            $table->text('example_sentence_romaji')->nullable();
            $table->text('example_sentence_id')->nullable();
            $table->string('audio_url', 500)->nullable();
            $table->timestamps();
            
            $table->index('category_uid');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabulary_words');
    }
};
