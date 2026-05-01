<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_credits', function (Blueprint $table) {
            // Add user_uid column
            $table->uuid('user_uid')->nullable()->after('uid');
            $table->index('user_uid');
        });

        // Populate user_uid from existing user_id relationships
        DB::statement('
            UPDATE user_credits uc
            INNER JOIN users u ON uc.user_id = u.id
            SET uc.user_uid = u.uid
        ');

        // Make user_uid not nullable and add foreign key
        Schema::table('user_credits', function (Blueprint $table) {
            $table->uuid('user_uid')->nullable(false)->change();
            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_credits', function (Blueprint $table) {
            $table->dropForeign(['user_uid']);
            $table->dropColumn('user_uid');
        });
    }
};
