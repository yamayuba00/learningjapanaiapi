<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Services\Mobile\AuthService;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debug Email Verification Issue ===\n\n";

// Test dengan user yang sudah ada
$email = 'bayupm124@gmail.com'; // Email dari error sebelumnya
$user = User::where('email', $email)->first();

if ($user) {
    echo "User found: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "UID: {$user->uid}\n";
    echo "Email Verified At: " . ($user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "Email Verified At (raw): " . ($user->email_verified_at ? 'NOT NULL' : 'NULL') . "\n";
    echo "Has Verified Email (method): " . ($user->hasVerifiedEmail() ? 'TRUE' : 'FALSE') . "\n";
    echo "Email Verification OTP: " . ($user->email_verification_otp ?? 'NULL') . "\n";
    echo "OTP Expires At: " . ($user->email_verification_otp_expires_at ? $user->email_verification_otp_expires_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "Created At: {$user->created_at->format('Y-m-d H:i:s')}\n";
    echo "Updated At: {$user->updated_at->format('Y-m-d H:i:s')}\n\n";
    
    // Test login
    echo "Testing login...\n";
    $authService = new AuthService();
    $loginResult = $authService->login([
        'email' => $email,
        'password' => 'password123' // Ganti dengan password yang benar
    ]);
    
    echo "Login result: " . ($loginResult['success'] ? 'SUCCESS' : 'FAILED') . "\n";
    echo "Login message: {$loginResult['message']}\n\n";
    
    // Manual check hasVerifiedEmail
    echo "Manual verification check:\n";
    echo "email_verified_at is null: " . (is_null($user->email_verified_at) ? 'TRUE' : 'FALSE') . "\n";
    echo "!is_null(email_verified_at): " . (!is_null($user->email_verified_at) ? 'TRUE' : 'FALSE') . "\n";
    
    // Check if there's an issue with the hasVerifiedEmail method
    echo "\nDebugging hasVerifiedEmail method:\n";
    $hasVerified = !is_null($user->email_verified_at);
    echo "Direct check result: " . ($hasVerified ? 'TRUE' : 'FALSE') . "\n";
    
    // If email_verified_at is null, let's manually set it for testing
    if (is_null($user->email_verified_at)) {
        echo "\nEmail not verified. Setting email_verified_at manually for testing...\n";
        $user->update(['email_verified_at' => now()]);
        $user->refresh();
        
        echo "After manual update:\n";
        echo "Email Verified At: " . ($user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
        echo "Has Verified Email: " . ($user->hasVerifiedEmail() ? 'TRUE' : 'FALSE') . "\n";
        
        // Test login again
        echo "\nTesting login after manual verification...\n";
        $loginResult2 = $authService->login([
            'email' => $email,
            'password' => 'password123'
        ]);
        
        echo "Login result: " . ($loginResult2['success'] ? 'SUCCESS' : 'FAILED') . "\n";
        echo "Login message: {$loginResult2['message']}\n";
    }
    
} else {
    echo "User with email $email not found!\n";
    
    // List all users for debugging
    echo "\nAll users in database:\n";
    $users = User::select(['name', 'email', 'email_verified_at'])->get();
    foreach ($users as $u) {
        echo "- {$u->name} ({$u->email}) - Verified: " . ($u->email_verified_at ? 'YES' : 'NO') . "\n";
    }
}

echo "\n=== Debug Complete ===\n";