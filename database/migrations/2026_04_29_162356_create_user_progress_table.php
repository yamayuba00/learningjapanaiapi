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
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->decimal('hiragana_score', 5, 2)->default(0.00);
            $table->decimal('katakana_score', 5, 2)->default(0.00);
            $table->decimal('vocabulary_score', 5, 2)->default(0.00);
            $table->decimal('n5_progress', 5, 2)->default(0.00);
            $table->decimal('n4_progress', 5, 2)->default(0.00);
            $table->decimal('n3_progress', 5, 2)->default(0.00);
            $table->decimal('n2_progress', 5, 2)->default(0.00);
            $table->decimal('n1_progress', 5, 2)->default(0.00);
            $table->integer('today_lessons')->default(0);
            $table->integer('yesterday_lessons')->default(0);
            $table->date('last_update_date')->nullable();
            $table->timestamps();
            
            $table->unique('user_uid');
            $table->index('user_uid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
