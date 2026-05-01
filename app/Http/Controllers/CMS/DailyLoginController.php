<?php

namespace App\Http\Controllers\CMS;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\CMS\DailyLoginService;
use Illuminate\Http\Request;

/**
 * Daily Login Controller for CMS (Admin)
 * Manages daily login data for all users
 */
class DailyLoginController extends Controller
{
    protected $dailyLoginService;

    public function __construct(DailyLoginService $dailyLoginService)
    {
        $this->dailyLoginService = $dailyLoginService;
    }

    /**
     * Get daily login status for specific user
     * 
     * @param string $userUid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserStatus($userUid)
    {
        try {
            $status = $this->dailyLoginService->getClaimStatus($userUid);

            return ResponseHelper::success($status, 'User daily login status retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get user daily login status: ' . $e->getMessage());
        }
    }

    /**
     * Get claim history for specific user
     * 
     * @param string $userUid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserHistory(Request $request, $userUid)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $history = $this->dailyLoginService->getHistory($userUid, $perPage);

            return ResponseHelper::success($history, 'User claim history retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get user claim history: ' . $e->getMessage());
        }
    }

    /**
     * Manually claim reward for user (Admin only)
     * 
     * @param string $userUid
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualClaim($userUid)
    {
        try {
            $result = $this->dailyLoginService->claimDailyReward($userUid);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success($result, 'Manual claim successful');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to manually claim reward: ' . $e->getMessage());
        }
    }
}
