<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Mobile\LeaderboardService;
use Illuminate\Http\Request;

/**
 * Leaderboard Controller for Mobile App
 * Handles monthly leaderboard with points from daily claims and ad watches
 */
class LeaderboardController extends Controller
{
    protected $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;
    }

    /**
     * Get top 10 leaderboard for current month
     */
    public function index(Request $request)
    {
        try {
            // Check and reset leaderboard if new month
            $this->leaderboardService->checkAndResetMonthlyLeaderboard();
            
            $result = $this->leaderboardService->getTopLeaderboard();

            if (!$result['success']) {
                return ResponseHelper::error($result['message']);
            }

            return ResponseHelper::success($result['data'], $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get leaderboard: ' . $e->getMessage());
        }
    }

    /**
     * Get current user's rank and stats
     */
    public function myRank(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            
            // Check and reset leaderboard if new month
            $this->leaderboardService->checkAndResetMonthlyLeaderboard();
            
            $result = $this->leaderboardService->getUserRank($userUid);

            if (!$result['success']) {
                return ResponseHelper::error($result['message']);
            }

            return ResponseHelper::success($result['data'], $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get rank: ' . $e->getMessage());
        }
    }
}
