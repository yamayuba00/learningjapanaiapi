<?php

namespace App\Http\Controllers\CMS;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Shared\UserCreditRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * User Credit Controller for CMS (Admin)
 * Manages credits for all users
 */
class UserCreditController extends Controller
{
    protected $creditRepository;

    public function __construct(UserCreditRepositoryInterface $creditRepository)
    {
        $this->creditRepository = $creditRepository;
    }

    /**
     * Get all users credits (paginated)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $credits = $this->creditRepository->getAllPaginated($perPage);

            return ResponseHelper::success($credits, 'Credits retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get credits: ' . $e->getMessage());
        }
    }

    /**
     * Get user credit by UID
     */
    public function show($userUid)
    {
        try {
            $credit = $this->creditRepository->findByUserUid($userUid);

            if (!$credit) {
                return ResponseHelper::notFound('Credit not found for this user');
            }

            return ResponseHelper::success($credit, 'Credit retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get credit: ' . $e->getMessage());
        }
    }

    /**
     * Add credits to user
     */
    public function addCredits(Request $request, $userUid)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $updated = $this->creditRepository->addCredits($userUid, $request->amount);

            if (!$updated) {
                return ResponseHelper::error('Failed to add credits');
            }

            return ResponseHelper::success([
                'user_uid' => $userUid,
                'amount_added' => $request->amount,
                'reason' => $request->reason,
            ], 'Credits added successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to add credits: ' . $e->getMessage());
        }
    }

    /**
     * Deduct credits from user
     */
    public function deductCredits(Request $request, $userUid)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $updated = $this->creditRepository->deductCredits($userUid, $request->amount);

            if (!$updated) {
                return ResponseHelper::error('Failed to deduct credits or insufficient balance');
            }

            return ResponseHelper::success([
                'user_uid' => $userUid,
                'amount_deducted' => $request->amount,
                'reason' => $request->reason,
            ], 'Credits deducted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to deduct credits: ' . $e->getMessage());
        }
    }

    /**
     * Add points to user
     */
    public function addPoints(Request $request, $userUid)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $updated = $this->creditRepository->addPoints($userUid, $request->amount);

            if (!$updated) {
                return ResponseHelper::error('Failed to add points');
            }

            return ResponseHelper::success([
                'user_uid' => $userUid,
                'points_added' => $request->amount,
                'reason' => $request->reason,
            ], 'Points added successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to add points: ' . $e->getMessage());
        }
    }

    /**
     * Update user streak
     */
    public function updateStreak(Request $request, $userUid)
    {
        $validator = Validator::make($request->all(), [
            'streak' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $updated = $this->creditRepository->updateStreak($userUid, $request->streak);

            if (!$updated) {
                return ResponseHelper::error('Failed to update streak');
            }

            return ResponseHelper::success([
                'user_uid' => $userUid,
                'new_streak' => $request->streak,
            ], 'Streak updated successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to update streak: ' . $e->getMessage());
        }
    }

    /**
     * Reset user cycle
     */
    public function resetCycle($userUid)
    {
        try {
            $updated = $this->creditRepository->resetCycle($userUid);

            if (!$updated) {
                return ResponseHelper::error('Failed to reset cycle');
            }

            return ResponseHelper::success([
                'user_uid' => $userUid,
            ], 'Cycle reset successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to reset cycle: ' . $e->getMessage());
        }
    }

    /**
     * Get top users by points
     */
    public function topUsers(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $topUsers = $this->creditRepository->getTopUsersByPoints($limit);

            return ResponseHelper::success($topUsers, 'Top users retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get top users: ' . $e->getMessage());
        }
    }

    /**
     * Get credit statistics
     */
    public function statistics()
    {
        try {
            // TODO: Implement statistics method in repository
            $stats = [
                'total_users' => 0,
                'total_credits_distributed' => 0,
                'total_points_earned' => 0,
                'average_credits_per_user' => 0,
            ];

            return ResponseHelper::success($stats, 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get statistics: ' . $e->getMessage());
        }
    }
}
