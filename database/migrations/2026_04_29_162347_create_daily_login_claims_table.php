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
        Schema::create('daily_login_claims', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('user_uid');
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->integer('cycle_number');
            $table->integer('day_index');
            $table->integer('points_earned');
            $table->integer('credits_earned');
            $table->timestamp('claimed_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['user_uid', 'cycle_number', 'day_index'], 'unique_claim');
            $table->index('user_uid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_login_claims');
    }
};
