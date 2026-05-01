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
        Schema::create('vocabulary_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->string('name', 100);
            $table->string('icon', 10)->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabulary_categories');
    }
};
