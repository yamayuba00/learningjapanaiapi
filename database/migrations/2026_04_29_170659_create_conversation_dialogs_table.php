<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_dialogs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->unsignedBigInteger('conversation_id');
            $table->uuid('conversation_uid');
            $table->string('speaker', 10);
            $table->text('japanese');
            $table->text('romaji');
            $table->text('indonesian');
            $table->integer('dialog_order');
            $table->string('audio_url', 500)->nullable();
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreign('conversation_uid')->references('uid')->on('conversations')->onDelete('cascade');
            $table->index(['conversation_uid', 'dialog_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_dialogs');
    }
};
