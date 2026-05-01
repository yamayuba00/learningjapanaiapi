<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserCredit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserCreditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users if they don't exist
        $user1 = User::firstOrCreate(
            ['email' => 'test1@example.com'],
            [
                'name' => 'Test User 1',
                'password' => Hash::make('password'),
                'phone_number' => '081234567890',
                'referal_code' => 'TEST001',
                'referal_by_code' => 'REF001',
                'uid' => \Illuminate\Support\Str::uuid(),
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'test2@example.com'],
            [
                'name' => 'Test User 2',
                'password' => Hash::make('password'),
                'phone_number' => '081234567891',
                'referal_code' => 'TEST002',
                'referal_by_code' => 'REF002',
                'uid' => \Illuminate\Support\Str::uuid(),
            ]
        );

        // Create user credits
        UserCredit::firstOrCreate(
            ['user_uid' => $user1->uid],
            [
                'uid' => \Illuminate\Support\Str::uuid(),
                'user_id' => $user1->id,
                'user_uid' => $user1->uid,
                'credits' => 100,
                'total_points' => 500,
                'streak' => 5,
                'cycle_number' => 1,
                'cycle_start_date' => now()->subDays(30),
                'last_claim_date' => now()->subDay(),
            ]
        );

        UserCredit::firstOrCreate(
            ['user_uid' => $user2->uid],
            [
                'uid' => \Illuminate\Support\Str::uuid(),
                'user_id' => $user2->id,
                'user_uid' => $user2->uid,
                'credits' => 250,
                'total_points' => 1000,
                'streak' => 10,
                'cycle_number' => 2,
                'cycle_start_date' => now()->subDays(60),
                'last_claim_date' => now(),
            ]
        );

        $this->command->info('User credits seeded successfully!');
        $this->command->info('Test User 1 UID: ' . ($user1->uid ?? 'N/A'));
        $this->command->info('Test User 2 UID: ' . ($user2->uid ?? 'N/A'));
        $this->command->info('Test User 1 Credit UID: ' . ($user1->credit->uid ?? 'N/A'));
        $this->command->info('Test User 2 Credit UID: ' . ($user2->credit->uid ?? 'N/A'));
    }
}
