<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\UserCredit;
use App\Services\Mobile\AuthService;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Referral Code Validation ===\n\n";

$authService = new AuthService();

// 1. Create unverified referrer user
echo "1. Creating unverified referrer user...\n";
$unverifiedReferrer = User::where('email', 'unverified@test.com')->first();
if ($unverifiedReferrer) {
    $unverifiedReferrer->delete();
}

$unverifiedReferrer = User::create([
    'name' => 'Unverified Referrer',
    'email' => 'unverified@test.com',
    'password' => bcrypt('password123'),
    'referal_code' => 'UNVERIFIED123',
    'referal_by_code' => 'SYSTEM',
    'email_verified_at' => null, // NOT VERIFIED
]);

UserCredit::create([
    'user_id' => $unverifiedReferrer->id,
    'user_uid' => $unverifiedReferrer->uid,
    'credits' => 0,
    'total_points' => 0,
    'streak' => 0,
    'cycle_number' => 1,
    'cycle_start_date' => now(),
]);

echo "✅ Unverified referrer created: {$unverifiedReferrer->referal_code}\n";
echo "   Email verified: " . ($unverifiedReferrer->hasVerifiedEmail() ? 'YES' : 'NO') . "\n\n";

// 2. Create verified referrer user
echo "2. Creating verified referrer user...\n";
$verifiedReferrer = User::where('email', 'verified@test.com')->first();
if ($verifiedReferrer) {
    $verifiedReferrer->delete();
}

$verifiedReferrer = User::create([
    'name' => 'Verified Referrer',
    'email' => 'verified@test.com',
    'password' => bcrypt('password123'),
    'referal_code' => 'VERIFIED123',
    'referal_by_code' => 'SYSTEM',
    'email_verified_at' => now(), // VERIFIED
]);

UserCredit::create([
    'user_id' => $verifiedReferrer->id,
    'user_uid' => $verifiedReferrer->uid,
    'credits' => 0,
    'total_points' => 0,
    'streak' => 0,
    'cycle_number' => 1,
    'cycle_start_date' => now(),
]);

echo "✅ Verified referrer created: {$verifiedReferrer->referal_code}\n";
echo "   Email verified: " . ($verifiedReferrer->hasVerifiedEmail() ? 'YES' : 'NO') . "\n\n";

// 3. Test registration with UNVERIFIED referral code (should FAIL)
echo "3. Testing registration with UNVERIFIED referral code (should FAIL)...\n";
User::where('email', 'newuser1@test.com')->delete();

$result1 = $authService->register([
    'name' => 'New User 1',
    'email' => 'newuser1@test.com',
    'password' => 'password123',
    'referral_code' => 'UNVERIFIED123', // Using unverified referrer's code
]);

echo "   Result: " . ($result1['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "   Message: {$result1['message']}\n\n";

// 4. Test registration with VERIFIED referral code (should SUCCESS)
echo "4. Testing registration with VERIFIED referral code (should SUCCESS)...\n";
User::where('email', 'newuser2@test.com')->delete();

$result2 = $authService->register([
    'name' => 'New User 2',
    'email' => 'newuser2@test.com',
    'password' => 'password123',
    'referral_code' => 'VERIFIED123', // Using verified referrer's code
]);

echo "   Result: " . ($result2['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "   Message: {$result2['message']}\n";

if ($result2['success']) {
    echo "   New user created with referral: {$result2['user']['referal_by_code']}\n";
}

echo "\n=== Test Results Summary ===\n";
echo "✅ Unverified referral code: " . ($result1['success'] ? 'INCORRECTLY ALLOWED' : 'CORRECTLY BLOCKED') . "\n";
echo "✅ Verified referral code: " . ($result2['success'] ? 'CORRECTLY ALLOWED' : 'INCORRECTLY BLOCKED') . "\n";

echo "\n=== Test Complete ===\n";