<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Mobile\DailyLoginService;
use Illuminate\Http\Request;

/**
 * Daily Login Controller for Mobile App
 * Handles daily login rewards for authenticated users
 */
class DailyLoginController extends Controller
{
    protected $dailyLoginService;

    public function __construct(DailyLoginService $dailyLoginService)
    {
        $this->dailyLoginService = $dailyLoginService;
    }

    /**
     * Get daily login status
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $status = $this->dailyLoginService->getClaimStatus($userUid);

            return ResponseHelper::success($status, 'Daily login status retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get daily login status: ' . $e->getMessage());
        }
    }

    /**
     * Claim daily login reward
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function claim(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $result = $this->dailyLoginService->claimDailyReward($userUid);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            // Extract only the data we need, not the entire service response
            $responseData = [
                'claim' => $result['claim'],
                'reward' => $result['reward'],
                'cycle_completed' => $result['cycle_completed']
            ];

            return ResponseHelper::success($responseData, $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to claim daily reward: ' . $e->getMessage());
        }
    }

    /**
     * Get claim history
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $perPage = $request->input('per_page', 15);
            
            $history = $this->dailyLoginService->getHistory($userUid, $perPage);

            return ResponseHelper::success($history, 'Claim history retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get claim history: ' . $e->getMessage());
        }
    }

    /**
     * Check if can claim today
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function canClaim(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $canClaim = $this->dailyLoginService->canClaimToday($userUid);

            return ResponseHelper::success($canClaim, 'Claim check completed');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to check claim status: ' . $e->getMessage());
        }
    }
}
