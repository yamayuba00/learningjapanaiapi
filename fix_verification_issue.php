<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Fixing Email Verification Issue ===\n\n";

// Check specific user
$email = 'bayupm124@gmail.com';
$user = User::where('email', $email)->first();

if ($user) {
    echo "Found user: {$user->name}\n";
    echo "Current email_verified_at: " . ($user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "hasVerifiedEmail(): " . ($user->hasVerifiedEmail() ? 'TRUE' : 'FALSE') . "\n\n";
    
    // If not verified, set it manually
    if (!$user->hasVerifiedEmail()) {
        echo "Setting email as verified...\n";
        $user->update([
            'email_verified_at' => now(),
            'email_verification_otp' => null,
            'email_verification_otp_expires_at' => null,
        ]);
        
        $user->refresh();
        echo "✅ Email verification updated\n";
        echo "New email_verified_at: " . $user->email_verified_at->format('Y-m-d H:i:s') . "\n";
        echo "hasVerifiedEmail(): " . ($user->hasVerifiedEmail() ? 'TRUE' : 'FALSE') . "\n";
    } else {
        echo "✅ Email already verified\n";
    }
} else {
    echo "User not found with email: $email\n";
}

echo "\n=== Fix Complete ===\n";