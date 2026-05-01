<?php

// Test script untuk forgot password
require_once 'vendor/autoload.php';

use App\Models\User;
use App\Services\Mobile\AuthService;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Forgot Password OTP ===\n\n";

// Cek apakah ada user dengan email test
$email = 'test@example.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "Creating test user with email: $email\n";
    $user = User::create([
        'name' => 'Test User',
        'email' => $email,
        'password' => bcrypt('password123'),
        'referal_code' => 'TEST123',
        'referal_by_code' => 'SYSTEM',
    ]);
    echo "✅ Test user created successfully!\n\n";
} else {
    echo "✅ Test user already exists: {$user->name}\n\n";
}

// Test AuthService
$authService = new AuthService();

echo "Sending password reset OTP to: $email\n";
$result = $authService->sendPasswordResetOTP($email);

if ($result['success']) {
    echo "✅ " . $result['message'] . "\n\n";
    
    // Ambil OTP dari database
    $user->refresh();
    $otp = $user->password_reset_otp;
    $expires = $user->password_reset_otp_expires_at;
    
    echo "📧 OTP Details:\n";
    echo "   Code: $otp\n";
    echo "   Expires: $expires\n\n";
    
    echo "🔍 Check your Mailtrap inbox at: https://mailtrap.io\n";
    echo "   Username: a30ba8118741e5\n";
    echo "   Look for email with subject: 'Password Reset - JapanAI'\n\n";
    
    echo "🧪 Test verify OTP:\n";
    $verifyResult = $authService->verifyPasswordResetOTP($email, $otp);
    if ($verifyResult['success']) {
        echo "✅ OTP verification: " . $verifyResult['message'] . "\n";
    } else {
        echo "❌ OTP verification failed: " . $verifyResult['message'] . "\n";
    }
    
} else {
    echo "❌ Failed: " . $result['message'] . "\n";
}

echo "\n=== Test Complete ===\n";