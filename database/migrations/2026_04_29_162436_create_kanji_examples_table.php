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
        Schema::create('kanji_examples', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('kanji_id')->constrained('kanji')->onDelete('cascade');
            $table->uuid('kanji_uid');
            $table->foreign('kanji_uid')->references('uid')->on('kanji')->onDelete('cascade');
            $table->string('word', 50);
            $table->string('reading', 100);
            $table->text('meaning');
            $table->timestamps();
            
            $table->index('kanji_uid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanji_examples');
    }
};
