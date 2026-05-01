<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->uuid('user_uid');
            $table->enum('quiz_type', ['hiragana', 'katakana', 'kanji', 'vocabulary']);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->integer('total_questions');
            $table->integer('correct_answers');
            $table->integer('wrong_answers');
            $table->decimal('score', 5, 2);
            $table->integer('time_spent_seconds');
            $table->integer('points_earned');
            $table->integer('lives_lost')->default(0);
            $table->timestamp('taken_at')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->index(['user_uid', 'quiz_type']);
            $table->index('taken_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_history');
    }
};
