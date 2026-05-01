<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'instagram' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|url',
            'referal_code' => 'nullable|string|unique:users,referal_code',
            'referal_by_code' => 'nullable|string|exists:users,referal_code',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $result = $this->authService->register($request->all());

        if (!$result['success']) {
            return ResponseHelper::error($result['message']);
        }

        return ResponseHelper::created([
            'user' => $result['user'],
        ], $result['message']);
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $result = $this->authService->login($request->only('email', 'password'));

        if (!$result['success']) {
            return ResponseHelper::unauthorized($result['message']);
        }

        return ResponseHelper::success([
            'user' => $result['user'],
            'token' => $result['token'],
            'token_type' => $result['token_type'],
        ], $result['message']);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $allDevices = $request->input('all_devices', false);
        
        $result = $this->authService->logout($request->user(), $allDevices);

        return ResponseHelper::success(null, $result['message']);
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $result = $this->authService->refreshToken($request->user());

        return ResponseHelper::success([
            'token' => $result['token'],
            'token_type' => $result['token_type'],
        ], $result['message']);
    }

    /**
     * Verify email
     */
    public function verifyEmail(string $token): JsonResponse
    {
        $result = $this->authService->verifyEmail($token);

        if (!$result['success']) {
            return ResponseHelper::error($result['message']);
        }

        return ResponseHelper::success(null, $result['message']);
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $result = $this->authService->resendVerificationEmail($request->email);

        if (!$result['success']) {
            return ResponseHelper::error($result['message']);
        }

        return ResponseHelper::success(null, $result['message']);
    }

    /**
     * Get user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $result = $this->authService->getProfile($request->user());

        return ResponseHelper::success($result['user'], 'Profile retrieved successfully');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'instagram' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $result = $this->authService->updateProfile($request->user(), $request->all());

        return ResponseHelper::success($result['user'], $result['message']);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $result = $this->authService->changePassword(
            $request->user(),
            $request->current_password,
            $request->new_password
        );

        if (!$result['success']) {
            return ResponseHelper::error($result['message']);
        }

        return ResponseHelper::success(null, $result['message']);
    }

    /**
     * Block user (Admin only)
     */
    public function blockUser(Request $request, string $uid): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $result = $this->authService->blockUser(
            $uid,
            $request->input('reason', 'Violation of terms')
        );

        if (!$result['success']) {
            return ResponseHelper::error($result['message']);
        }

        return ResponseHelper::success(null, $result['message']);
    }

    /**
     * Unblock user (Admin only)
     */
    public function unblockUser(string $uid): JsonResponse
    {
        $result = $this->authService->unblockUser($uid);

        if (!$result['success']) {
            return ResponseHelper::error($result['message']);
        }

        return ResponseHelper::success(null, $result['message']);
    }
}

