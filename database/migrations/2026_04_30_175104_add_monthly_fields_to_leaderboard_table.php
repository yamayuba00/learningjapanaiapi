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
        Schema::table('leaderboard', function (Blueprint $table) {
            $table->string('month_year', 7)->after('rank')->default('2026-04'); // Format: YYYY-MM
            $table->integer('claims_count')->after('month_year')->default(0);
            $table->integer('ads_count')->after('claims_count')->default(0);
            
            // Add index for better performance
            $table->index(['month_year', 'rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaderboard', function (Blueprint $table) {
            $table->dropIndex(['month_year', 'rank']);
            $table->dropColumn(['month_year', 'claims_count', 'ads_count']);
        });
    }
};