<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Repositories\UserCreditRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserCreditController extends Controller
{
    protected UserCreditRepositoryInterface $userCreditRepository;

    public function __construct(UserCreditRepositoryInterface $userCreditRepository)
    {
        $this->userCreditRepository = $userCreditRepository;
    }

    /**
     * Get authenticated user's credit
     */
    public function myCredit(Request $request): JsonResponse
    {
        $user = $request->user();
        $userCredit = $this->userCreditRepository->findByUserUid($user->uid);

        if (!$userCredit) {
            return ResponseHelper::notFound('User credit not found');
        }

        return ResponseHelper::success($userCredit, 'User credit retrieved successfully');
    }

    /**
     * Add credits to authenticated user
     */
    public function addMyCredits(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $user = $request->user();
        $success = $this->userCreditRepository->addCredits($user->uid, $request->amount);

        if (!$success) {
            return ResponseHelper::error('Failed to add credits');
        }

        return ResponseHelper::success(null, 'Credits added successfully');
    }

    /**
     * Deduct credits from authenticated user
     */
    public function deductMyCredits(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $user = $request->user();
        $success = $this->userCreditRepository->deductCredits($user->uid, $request->amount);

        if (!$success) {
            return ResponseHelper::error('Failed to deduct credits or insufficient balance');
        }

        return ResponseHelper::success(null, 'Credits deducted successfully');
    }

    /**
     * Update authenticated user's streak
     */
    public function updateMyStreak(Request $request): JsonResponse
    {
        $user = $request->user();
        $success = $this->userCreditRepository->updateStreak($user->uid);

        if (!$success) {
            return ResponseHelper::error('Failed to update streak');
        }

        return ResponseHelper::success(null, 'Streak updated successfully');
    }

    /**
     * Reset authenticated user's cycle
     */
    public function resetMyCycle(Request $request): JsonResponse
    {
        $user = $request->user();
        $success = $this->userCreditRepository->resetCycle($user->uid);

        if (!$success) {
            return ResponseHelper::error('Failed to reset cycle');
        }

        return ResponseHelper::success(null, 'Cycle reset successfully');
    }

    // ============================================
    // ADMIN ENDPOINTS (for managing other users)
    // ============================================

    /**
     * Get user credit by user UID (Admin)
     */
    public function showByUserUid(string $userUid): JsonResponse
    {
        $userCredit = $this->userCreditRepository->findByUserUid($userUid);

        if (!$userCredit) {
            return ResponseHelper::notFound('User credit not found');
        }

        return ResponseHelper::success($userCredit, 'User credit retrieved successfully');
    }

    /**
     * Add credits to user by user UID (Admin)
     */
    public function addCredits(Request $request, string $userUid): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $success = $this->userCreditRepository->addCredits($userUid, $request->amount);

        if (!$success) {
            return ResponseHelper::error('Failed to add credits');
        }

        return ResponseHelper::success(null, 'Credits added successfully');
    }

    /**
     * Deduct credits from user by user UID (Admin)
     */
    public function deductCredits(Request $request, string $userUid): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $success = $this->userCreditRepository->deductCredits($userUid, $request->amount);

        if (!$success) {
            return ResponseHelper::error('Failed to deduct credits or insufficient balance');
        }

        return ResponseHelper::success(null, 'Credits deducted successfully');
    }

    /**
     * Update user streak by user UID (Admin)
     */
    public function updateStreak(string $userUid): JsonResponse
    {
        $success = $this->userCreditRepository->updateStreak($userUid);

        if (!$success) {
            return ResponseHelper::error('Failed to update streak');
        }

        return ResponseHelper::success(null, 'Streak updated successfully');
    }

    /**
     * Reset user cycle by user UID (Admin)
     */
    public function resetCycle(string $userUid): JsonResponse
    {
        $success = $this->userCreditRepository->resetCycle($userUid);

        if (!$success) {
            return ResponseHelper::error('Failed to reset cycle');
        }

        return ResponseHelper::success(null, 'Cycle reset successfully');
    }
}

