<?php

namespace App\Http\Controllers\CMS;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\CMS\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Authentication Controller for CMS (Admin)
 * Handles admin authentication and user management
 */
class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Admin login
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
            $result = $this->authService->login($request->email, $request->password);
            
            if (!$result['success']) {
                return ResponseHelper::unauthorized($result['message']);
            }

            // TODO: Add admin role check here
            // if (!$result['user']->is_admin) {
            //     return ResponseHelper::forbidden('Access denied. Admin only.');
            // }

            return ResponseHelper::success($result, 'Admin login successful');
        } catch (\Exception $e) {
            return ResponseHelper::error('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * Admin logout
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
     * Get admin profile
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
     * Block user account
     */
    public function blockUser(Request $request, $userUid)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $result = $this->authService->blockUser($userUid, $request->reason);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success($result, 'User blocked successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to block user: ' . $e->getMessage());
        }
    }

    /**
     * Unblock user account
     */
    public function unblockUser($userUid)
    {
        try {
            $result = $this->authService->unblockUser($userUid);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success($result, 'User unblocked successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to unblock user: ' . $e->getMessage());
        }
    }

    /**
     * Get user details (Admin view)
     */
    public function getUserDetails($userUid)
    {
        try {
            $profile = $this->authService->getProfile($userUid);
            return ResponseHelper::success($profile, 'User details retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get user details: ' . $e->getMessage());
        }
    }

    /**
     * Get all users (Admin view)
     */
    public function getAllUsers(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $search = $request->input('search');
            $status = $request->input('status'); // 'active', 'blocked', 'unverified'

            // TODO: Implement in AuthService
            // $users = $this->authService->getAllUsers($perPage, $search, $status);

            return ResponseHelper::success([], 'Users retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get users: ' . $e->getMessage());
        }
    }
}
