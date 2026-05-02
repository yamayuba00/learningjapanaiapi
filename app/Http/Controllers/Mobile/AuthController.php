<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Mobile\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Authentication Controller for Mobile App
 * Handles user authentication and profile management
 */
class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'instagram' => 'nullable|string|max:100',
            'referral_code' => 'nullable|string|max:20|exists:users,referal_code',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $result = $this->authService->register($request->all());
            return ResponseHelper::success($result, 'Registration successful. Please check your email to verify your account.', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];
            
            $result = $this->authService->login($credentials);
            
            if (!$result['success']) {
                return ResponseHelper::unauthorized($result['message']);
            }

            return ResponseHelper::success($result, 'Login successful');
        } catch (\Exception $e) {
            return ResponseHelper::error('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request->user());
            return ResponseHelper::success(null, 'Logout successful');
        } catch (\Exception $e) {
            return ResponseHelper::error('Logout failed: ' . $e->getMessage());
        }
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            $profile = $this->authService->getProfile($user->uid);
            return ResponseHelper::success($profile, 'Profile retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get profile: ' . $e->getMessage());
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'instagram' => 'nullable|string|max:100',
            'avatar_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $user = $request->user();
            $updated = $this->authService->updateProfile($user->uid, $request->all());
            return ResponseHelper::success($updated, 'Profile updated successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $user = $request->user();
            $result = $this->authService->changePassword(
                $user->uid,
                $request->current_password,
                $request->new_password
            );

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success(null, 'Password changed successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to change password: ' . $e->getMessage());
        }
    }

    /**
     * Verify email with OTP
     */
    public function verifyEmailWithOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $result = $this->authService->verifyEmailWithOTP($request->email, $request->otp);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success(null, $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Email verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Verify email
     */
    public function verifyEmail($token)
    {
        try {
            $result = $this->authService->verifyEmail($token);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success($result, 'Email verified successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Email verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $result = $this->authService->resendVerificationEmail($request->email);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success(null, 'Verification email sent successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to resend verification email: ' . $e->getMessage());
        }
    }

    /**
     * Send password reset OTP
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $result = $this->authService->sendPasswordResetOTP($request->email);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success(null, $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to send reset code: ' . $e->getMessage());
        }
    }

    /**
     * Verify password reset OTP
     */
    public function verifyResetOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $result = $this->authService->verifyPasswordResetOTP($request->email, $request->otp);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success(null, $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to verify reset code: ' . $e->getMessage());
        }
    }

    /**
     * Reset password with OTP
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $result = $this->authService->resetPasswordWithOTP(
                $request->email,
                $request->otp,
                $request->new_password
            );

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success(null, $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to reset password: ' . $e->getMessage());
        }
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        try {
            $user = $request->user();
            $result = $this->authService->refreshToken($user);
            return ResponseHelper::success($result, 'Token refreshed successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to refresh token: ' . $e->getMessage());
        }
    }
}
