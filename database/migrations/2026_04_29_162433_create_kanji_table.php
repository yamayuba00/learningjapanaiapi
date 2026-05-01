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
        Schema::create('kanji', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->string('character', 10)->unique();
            $table->text('meaning');
            $table->string('onyomi', 100)->nullable();
            $table->string('kunyomi', 100)->nullable();
            $table->enum('level', ['N5', 'N4', 'N3', 'N2', 'N1']);
            $table->integer('stroke_count');
            $table->string('stroke_order_gif', 500)->nullable();
            $table->string('radicals', 100)->nullable();
            $table->timestamps();
            
            $table->index('level');
            $table->index('character');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanji');
    }
};
