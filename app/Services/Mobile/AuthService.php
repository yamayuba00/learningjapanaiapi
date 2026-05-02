<?php

namespace App\Services\Mobile;

use App\Models\User;
use App\Models\UserCredit;
use App\Models\ReferralReward;
use App\Mail\EmailVerificationOTP;
use App\Mail\PasswordResetOTP;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthService
{
    /**
     * Register a new user
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        try {
            DB::beginTransaction();

            // Generate referral code if not provided
            if (empty($data['referal_code'])) {
                $data['referal_code'] = $this->generateReferralCode();
            }

            // Validate referral code if provided
            $referrerUser = null;
            if (!empty($data['referral_code'])) {
                $referrerUser = User::where('referal_code', $data['referral_code'])->first();
                if (!$referrerUser) {
                    return [
                        'success' => false,
                        'message' => 'Invalid referral code'
                    ];
                }
                
                // Check if referrer has verified their email
                if (!$referrerUser->hasVerifiedEmail()) {
                    return [
                        'success' => false,
                        'message' => 'Referral code is not active. The referrer must verify their email first.'
                    ];
                }
            }

            // Generate email verification OTP
            $otp = $this->generateOTP();

            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'avatar_url' => $data['avatar_url'] ?? null,
                'referal_code' => $data['referal_code'],
                'referal_by_code' => $data['referral_code'] ?? 'SYSTEM',
                'email_verification_otp' => $otp,
                'email_verification_otp_expires_at' => now()->addMinutes(10),
                'email_verification_sent_at' => now(),
            ]);

            // Create user credit record (initial credits = 0, will be added after email verification)
            UserCredit::create([
                'user_id' => $user->id,
                'user_uid' => $user->uid,
                'credits' => 0,
                'total_points' => 0,
                'streak' => 0,
                'cycle_number' => 1,
                'cycle_start_date' => now(),
            ]);

            // Send verification email with OTP
            $this->sendVerificationOTP($user, $otp);

            DB::commit();

            return [
                'success' => true,
                'user' => $user,
                'message' => 'Registration successful. Please check your email for the verification code.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Login user
     *
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        // Check if user is blocked
        if ($user->isBlocked()) {
            return [
                'success' => false,
                'message' => 'Your account has been blocked. Reason: ' . $user->blocked_reason
            ];
        }

        // Check if email is verified (REQUIRED)
        if (!$user->hasVerifiedEmail()) {
            Log::warning("Login blocked - email not verified for user: {$user->uid}, email: {$user->email}");
            Log::warning("email_verified_at value: " . ($user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : 'NULL'));
            return [
                'success' => false,
                'message' => 'Please verify your email before logging in'
            ];
        }

        // Update last login
        $user->update(['last_login' => now()]);

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Login successful'
        ];
    }

    /**
     * Logout user
     *
     * @param User $user
     * @param bool $allDevices
     * @return array
     */
    public function logout(User $user, bool $allDevices = false): array
    {
        if ($allDevices) {
            // Revoke all tokens
            $user->tokens()->delete();
            $message = 'Logged out from all devices successfully';
        } else {
            // Revoke current token
            $user->currentAccessToken()->delete();
            $message = 'Logged out successfully';
        }

        return [
            'success' => true,
            'message' => $message
        ];
    }

    /**
     * Refresh token
     *
     * @param User $user
     * @return array
     */
    public function refreshToken(User $user): array
    {
        // Revoke current token
        $user->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Token refreshed successfully'
        ];
    }

    /**
     * Verify email with OTP
     *
     * @param string $email
     * @param string $otp
     * @return array
     */
    public function verifyEmailWithOTP(string $email, string $otp): array
    {
        try {
            DB::beginTransaction();

            $user = User::where('email', $email)->first();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }

            if ($user->hasVerifiedEmail()) {
                return [
                    'success' => false,
                    'message' => 'Email already verified'
                ];
            }

            if (!$user->email_verification_otp || $user->email_verification_otp !== $otp) {
                return [
                    'success' => false,
                    'message' => 'Invalid verification code'
                ];
            }

            // Check if OTP is expired
            if ($user->email_verification_otp_expires_at && $user->email_verification_otp_expires_at->isPast()) {
                return [
                    'success' => false,
                    'message' => 'Verification code has expired. Please request a new one.'
                ];
            }

            // Mark email as verified
            $user->update([
                'email_verified_at' => now(),
                'email_verification_otp' => null,
                'email_verification_otp_expires_at' => null,
            ]);

            // Log the verification
            Log::info("Email verified for user: {$user->uid}, email: {$user->email}");
            
            // Refresh user to get updated data
            $user->refresh();
            
            // Verify the update worked
            Log::info("After refresh - email_verified_at: " . ($user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : 'NULL'));
            Log::info("After refresh - hasVerifiedEmail(): " . ($user->hasVerifiedEmail() ? 'TRUE' : 'FALSE'));

            // Process referral rewards if user was referred
            if ($user->referal_by_code && $user->referal_by_code !== 'SYSTEM') {
                $this->processReferralRewards($user);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Email verified successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Email verification failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify email
     *
     * @param string $token
     * @return array
     */
    public function verifyEmail(string $token): array
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid verification token'
            ];
        }

        if ($user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => 'Email already verified'
            ];
        }

        // Check if token is expired (24 hours)
        if ($user->email_verification_sent_at->addHours(24)->isPast()) {
            return [
                'success' => false,
                'message' => 'Verification token has expired. Please request a new one.'
            ];
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);

        return [
            'success' => true,
            'message' => 'Email verified successfully'
        ];
    }

    /**
     * Resend verification email
     *
     * @param string $email
     * @return array
     */
    public function resendVerificationEmail(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        if ($user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => 'Email already verified'
            ];
        }

        // Generate new OTP
        $otp = $this->generateOTP();

        $user->update([
            'email_verification_otp' => $otp,
            'email_verification_otp_expires_at' => now()->addMinutes(10),
            'email_verification_sent_at' => now(),
        ]);

        // Send verification email with new OTP
        $this->sendVerificationOTP($user, $otp);

        return [
            'success' => true,
            'message' => 'Verification code sent successfully'
        ];
    }

    /**
     * Block user account
     *
     * @param string $uid
     * @param string $reason
     * @return array
     */
    public function blockUser(string $uid, string $reason = 'Violation of terms'): array
    {
        $user = User::where('uid', $uid)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        if ($user->isBlocked()) {
            return [
                'success' => false,
                'message' => 'User is already blocked'
            ];
        }

        $user->update([
            'is_blocked' => true,
            'blocked_at' => now(),
            'blocked_reason' => $reason,
        ]);

        // Revoke all tokens
        $user->tokens()->delete();

        return [
            'success' => true,
            'message' => 'User blocked successfully'
        ];
    }

    /**
     * Unblock user account
     *
     * @param string $uid
     * @return array
     */
    public function unblockUser(string $uid): array
    {
        $user = User::where('uid', $uid)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        if (!$user->isBlocked()) {
            return [
                'success' => false,
                'message' => 'User is not blocked'
            ];
        }

        $user->update([
            'is_blocked' => false,
            'blocked_at' => null,
            'blocked_reason' => null,
        ]);

        return [
            'success' => true,
            'message' => 'User unblocked successfully'
        ];
    }

    /**
     * Get user profile
     *
     * @param string $uid
     * @return array
     */
    public function getProfile(string $uid): array
    {
        $user = User::where('uid', $uid)->with('credit')->first();
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        // Clean profile data - only essential fields
        $profileData = [
            'uid' => $user->uid,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'instagram' => $user->instagram,
            'avatar_url' => $user->avatar_url,
            'referal_code' => $user->referal_code,
            'email_verified' => $user->hasVerifiedEmail(),
            'is_blocked' => $user->is_blocked,
            'member_since' => $user->created_at->format('Y-m-d'),
            'credits' => [
                'current' => $user->credit ? $user->credit->credits : 0,
                'total_earned' => $user->credit ? $user->credit->total_points : 0,
                'streak' => $user->credit ? $user->credit->streak : 0,
            ]
        ];

        return [
            'success' => true,
            'profile' => $profileData
        ];
    }

    /**
     * Update user profile
     *
     * @param string $uid
     * @param array $data
     * @return array
     */
    public function updateProfile(string $uid, array $data): array
    {
        $user = User::where('uid', $uid)->with('credit')->first();
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['phone_number'])) {
            $updateData['phone_number'] = $data['phone_number'];
        }

        if (isset($data['instagram'])) {
            $updateData['instagram'] = $data['instagram'];
        }

        if (isset($data['avatar_url'])) {
            $updateData['avatar_url'] = $data['avatar_url'];
        }

        $user->update($updateData);
        $user->refresh();

        // Return clean profile data - same format as getProfile
        $profileData = [
            'uid' => $user->uid,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'instagram' => $user->instagram,
            'avatar_url' => $user->avatar_url,
            'referal_code' => $user->referal_code,
            'email_verified' => $user->hasVerifiedEmail(),
            'is_blocked' => $user->is_blocked,
            'member_since' => $user->created_at->format('Y-m-d'),
            'credits' => [
                'current' => $user->credit ? $user->credit->credits : 0,
                'total_earned' => $user->credit ? $user->credit->total_points : 0,
                'streak' => $user->credit ? $user->credit->streak : 0,
            ]
        ];

        return [
            'success' => true,
            'profile' => $profileData,
            'message' => 'Profile updated successfully'
        ];
    }

    /**
     * Change password
     *
     * @param string $uid
     * @param string $currentPassword
     * @param string $newPassword
     * @return array
     */
    public function changePassword(string $uid, string $currentPassword, string $newPassword): array
    {
        $user = User::where('uid', $uid)->first();
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        if (!Hash::check($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect'
            ];
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Revoke all tokens except current
        $currentToken = $user->currentAccessToken();
        $user->tokens()->where('id', '!=', $currentToken->id)->delete();

        return [
            'success' => true,
            'message' => 'Password changed successfully'
        ];
    }

    /**
     * Send password reset OTP
     *
     * @param string $email
     * @return array
     */
    public function sendPasswordResetOTP(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User with this email not found'
            ];
        }

        // Generate OTP for password reset
        $otp = $this->generateOTP();

        $user->update([
            'password_reset_otp' => $otp,
            'password_reset_otp_expires_at' => now()->addMinutes(15), // 15 minutes for password reset
        ]);

        // Send password reset email with OTP
        $this->sendPasswordResetOTPEmail($user, $otp);

        return [
            'success' => true,
            'message' => 'Password reset code sent to your email'
        ];
    }

    /**
     * Verify password reset OTP
     *
     * @param string $email
     * @param string $otp
     * @return array
     */
    public function verifyPasswordResetOTP(string $email, string $otp): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        if (!$user->password_reset_otp || $user->password_reset_otp !== $otp) {
            return [
                'success' => false,
                'message' => 'Invalid reset code'
            ];
        }

        // Check if OTP is expired
        if ($user->password_reset_otp_expires_at && $user->password_reset_otp_expires_at->isPast()) {
            return [
                'success' => false,
                'message' => 'Reset code has expired. Please request a new one.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Reset code verified successfully'
        ];
    }

    /**
     * Reset password with OTP
     *
     * @param string $email
     * @param string $otp
     * @param string $newPassword
     * @return array
     */
    public function resetPasswordWithOTP(string $email, string $otp, string $newPassword): array
    {
        // First verify the OTP
        $verifyResult = $this->verifyPasswordResetOTP($email, $otp);
        
        if (!$verifyResult['success']) {
            return $verifyResult;
        }

        $user = User::where('email', $email)->first();

        // Update password and clear OTP
        $user->update([
            'password' => Hash::make($newPassword),
            'password_reset_otp' => null,
            'password_reset_otp_expires_at' => null,
        ]);

        // Revoke all existing tokens for security
        $user->tokens()->delete();

        return [
            'success' => true,
            'message' => 'Password reset successfully. Please login with your new password.'
        ];
    }

    /**
     * Check if email already exists
     *
     * @param string $email
     * @return array
     */
    

    /**
     * Process referral rewards when user verifies email
     *
     * @param User $newUser
     * @return void
     */
    private function processReferralRewards(User $newUser): void
    {
        // Find referrer user
        $referrerUser = User::where('referal_code', $newUser->referal_by_code)->first();
        
        if (!$referrerUser) {
            Log::warning("Referrer not found for code: {$newUser->referal_by_code}");
            return;
        }

        // Check if reward already processed (prevent duplicate)
        $existingReward = ReferralReward::where('referred_user_uid', $newUser->uid)->first();
        if ($existingReward) {
            Log::info("Referral reward already processed for user: {$newUser->uid}");
            return;
        }

        // Add credits to referrer (100 credits)
        $referrerCredit = UserCredit::where('user_uid', $referrerUser->uid)->first();
        if ($referrerCredit) {
            $referrerCredit->increment('credits', 100);
            $referrerCredit->increment('total_points', 100);
        }

        // Add credits to new user (40 credits)
        $newUserCredit = UserCredit::where('user_uid', $newUser->uid)->first();
        if ($newUserCredit) {
            $newUserCredit->increment('credits', 40);
            $newUserCredit->increment('total_points', 40);
        }

        // Create referral reward record
        ReferralReward::create([
            'referrer_user_id' => $referrerUser->id,
            'referrer_user_uid' => $referrerUser->uid,
            'referred_user_id' => $newUser->id,
            'referred_user_uid' => $newUser->uid,
            'referrer_credits_earned' => 100,
            'referred_credits_earned' => 40,
        ]);

        Log::info("Referral rewards processed: Referrer {$referrerUser->uid} (+100), New user {$newUser->uid} (+40)");
    }

    /**
     * Generate unique referral code
     *
     * @return string
     */
    private function generateReferralCode(): string
    {
        do {
            $code = 'REF' . strtoupper(Str::random(6));
        } while (User::where('referal_code', $code)->exists());

        return $code;
    }

    /**
     * Generate OTP code
     *
     * @return string
     */
    private function generateOTP(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send password reset email with OTP
     *
     * @param User $user
     * @param string $otp
     * @return void
     */
    private function sendPasswordResetOTPEmail(User $user, string $otp): void
    {
        try {
            Mail::to($user->email)->send(new PasswordResetOTP($user, $otp));
            Log::info('Password reset OTP email sent to: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset OTP email: ' . $e->getMessage());
            // Don't throw exception to prevent process failure
        }
    }

    /**
     * Send verification email with OTP
     *
     * @param User $user
     * @param string $otp
     * @return void
     */
    private function sendVerificationOTP(User $user, string $otp): void
    {
        try {
            Mail::to($user->email)->send(new EmailVerificationOTP($user, $otp));
            Log::info('Verification OTP email sent to: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send verification OTP email: ' . $e->getMessage());
            // Don't throw exception to prevent registration failure
        }
    }

    /**
     * Send verification email
     *
     * @param User $user
     * @param string $token
     * @return void
     */
    private function sendVerificationEmail(User $user, string $token): void
    {
        // TODO: Implement actual email sending
        // For now, we'll just log it
        Log::info('Verification email sent to: ' . $user->email);
        Log::info('Verification token: ' . $token);
        Log::info('Verification URL: ' . url('/api/auth/verify-email/' . $token));

        // Example implementation:
        // Mail::to($user->email)->send(new VerificationEmail($user, $token));
    }
}
