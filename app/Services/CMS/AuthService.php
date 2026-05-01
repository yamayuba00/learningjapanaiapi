<?php

namespace App\Services\CMS;

use App\Models\User;
use App\Models\UserCredit;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * CMS Auth Service
 * Same as Mobile but for CMS admin panel
 */
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

            // Generate email verification token
            $verificationToken = Str::random(64);

            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'avatar_url' => $data['avatar_url'] ?? null,
                'referal_code' => $data['referal_code'],
                'referal_by_code' => $data['referal_by_code'] ?? 'SYSTEM',
                'email_verification_token' => $verificationToken,
                'email_verification_sent_at' => now(),
            ]);

            // Create user credit record
            UserCredit::create([
                'user_id' => $user->id,
                'user_uid' => $user->uid,
                'credits' => 0,
                'total_points' => 0,
                'streak' => 0,
                'cycle_number' => 1,
                'cycle_start_date' => now(),
            ]);

            // Send verification email
            $this->sendVerificationEmail($user, $verificationToken);

            DB::commit();

            return [
                'success' => true,
                'user' => $user,
                'message' => 'Registration successful. Please check your email to verify your account.'
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

        // Update last login
        $user->update(['last_login' => now()]);

        // Create token
        $token = $user->createToken('cms_auth_token')->plainTextToken;

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
        $token = $user->createToken('cms_auth_token')->plainTextToken;

        return [
            'success' => true,
            'token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Token refreshed successfully'
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
     * Get all users with pagination
     *
     * @param int $perPage
     * @return array
     */
    public function getAllUsers(int $perPage = 15): array
    {
        $users = User::with('credit')->paginate($perPage);

        return [
            'success' => true,
            'users' => $users
        ];
    }

    /**
     * Get user by UID
     *
     * @param string $uid
     * @return array
     */
    public function getUserByUid(string $uid): array
    {
        $user = User::with('credit')->where('uid', $uid)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        return [
            'success' => true,
            'user' => $user
        ];
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
        \Log::info('Verification email sent to: ' . $user->email);
        \Log::info('Verification token: ' . $token);
        \Log::info('Verification URL: ' . url('/api/auth/verify-email/' . $token));

        // Example implementation:
        // Mail::to($user->email)->send(new VerificationEmail($user, $token));
    }
}
