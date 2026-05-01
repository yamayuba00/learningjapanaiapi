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
        Schema::create('partnership_internships', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->string('name');
            $table->text('description');
            $table->string('logo_url', 500)->nullable();
            $table->string('website', 500);
            $table->json('programs'); // Array of internship programs
            $table->json('benefits'); // Array of benefits
            $table->string('contact_whatsapp', 20);
            $table->string('contact_instagram', 100);
            $table->boolean('is_verified')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('total_alumni')->default(0);
            $table->decimal('success_rate', 5, 2)->default(0.00);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnership_internships');
    }
};
