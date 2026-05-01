<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->unsignedBigInteger('referrer_user_id');
            $table->uuid('referrer_user_uid');
            $table->unsignedBigInteger('referred_user_id');
            $table->uuid('referred_user_uid');
            $table->integer('referrer_credits_earned')->default(100);
            $table->integer('referred_credits_earned')->default(40);
            $table->timestamps();

            $table->foreign('referrer_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referrer_user_uid')->references('uid')->on('users')->onDelete('cascade');
            $table->foreign('referred_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referred_user_uid')->references('uid')->on('users')->onDelete('cascade');
            
            $table->unique(['referrer_user_uid', 'referred_user_uid'], 'unique_referral');
            $table->index('referrer_user_uid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
    }
};
