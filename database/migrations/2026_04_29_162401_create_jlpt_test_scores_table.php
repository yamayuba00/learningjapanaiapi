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
        Schema::create('jlpt_test_scores', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->enum('level', ['N5', 'N4', 'N3', 'N2', 'N1']);
            $table->enum('test_type', ['pretest', 'exam']);
            $table->decimal('score', 5, 2);
            $table->integer('total_questions');
            $table->integer('correct_answers');
            $table->timestamp('taken_at')->useCurrent();
            $table->timestamps();
            
            $table->index('user_uid');
            $table->index(['user_uid', 'level', 'test_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jlpt_test_scores');
    }
};
