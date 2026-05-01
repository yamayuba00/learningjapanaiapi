<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->enum('type', ['hiragana', 'katakana', 'vocabulary']);
            $table->string('title');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'difficulty']);
            $table->index('display_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
