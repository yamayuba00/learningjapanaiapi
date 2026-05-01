<?php

namespace App\Services\Mobile;

use App\Models\Leaderboard;
use App\Models\User;
use App\Models\DailyLoginClaim;
use App\Models\AdWatch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Leaderboard Service for Mobile App
 * Handles monthly leaderboard calculation and ranking
 */
class LeaderboardService
{
    const POINTS_PER_CLAIM = 20;
    const POINTS_PER_AD_WATCH = 20;
    const TOP_LIMIT = 10;

    /**
     * Calculate and update monthly leaderboard
     */
    public function calculateMonthlyLeaderboard(): void
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Clear current leaderboard for this month
        Leaderboard::truncate();

        // Calculate points for each user this month
        $userPoints = $this->calculateUserPoints($startOfMonth, $endOfMonth);

        // Sort by points descending and assign ranks
        $userPoints = $userPoints->sortByDesc('total_points')->values();

        // Insert leaderboard entries
        foreach ($userPoints as $index => $userData) {
            Leaderboard::create([
                'user_uid' => $userData['user_uid'],
                'user_id' => $userData['user_id'],
                'total_points' => $userData['total_points'],
                'rank' => $index + 1,
                'month_year' => $currentMonth,
                'claims_count' => $userData['claims_count'],
                'ads_count' => $userData['ads_count'],
            ]);
        }
    }

    /**
     * Calculate points for all users in given period
     */
    private function calculateUserPoints(Carbon $startDate, Carbon $endDate)
    {
        // Get all users with their activities this month
        $users = User::select('id', 'uid', 'name', 'avatar_url')
            ->with([
                'dailyLoginClaims' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('claimed_at', [$startDate, $endDate]);
                },
                'adWatches' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('watched_at', [$startDate, $endDate]);
                }
            ])
            ->get();

        $userPoints = collect();

        foreach ($users as $user) {
            $claimsCount = $user->dailyLoginClaims->count();
            $adsCount = $user->adWatches->count();
            $totalPoints = ($claimsCount * self::POINTS_PER_CLAIM) + ($adsCount * self::POINTS_PER_AD_WATCH);

            // Only include users with points > 0
            if ($totalPoints > 0) {
                $userPoints->push([
                    'user_id' => $user->id,
                    'user_uid' => $user->uid,
                    'user_name' => $user->name,
                    'user_avatar' => $user->avatar_url,
                    'claims_count' => $claimsCount,
                    'ads_count' => $adsCount,
                    'total_points' => $totalPoints,
                ]);
            }
        }

        return $userPoints;
    }

    /**
     * Get top 10 leaderboard
     */
    public function getTopLeaderboard(): array
    {
        $leaderboard = Leaderboard::with(['user:uid,name,avatar_url'])
            ->orderBy('rank', 'asc')
            ->limit(self::TOP_LIMIT)
            ->get();

        return [
            'success' => true,
            'data' => $leaderboard,
            'message' => 'Top leaderboard retrieved successfully'
        ];
    }

    /**
     * Get user's rank and position
     */
    public function getUserRank(string $userUid): array
    {
        $userEntry = Leaderboard::with(['user:uid,name,avatar_url'])
            ->where('user_uid', $userUid)
            ->first();

        if (!$userEntry) {
            // Calculate user's current points for this month
            $currentPoints = $this->calculateCurrentUserPoints($userUid);
            
            return [
                'success' => true,
                'data' => [
                    'user_uid' => $userUid,
                    'rank' => null,
                    'total_points' => $currentPoints['total_points'],
                    'claims_count' => $currentPoints['claims_count'],
                    'ads_count' => $currentPoints['ads_count'],
                    'is_in_top' => false,
                    'message' => 'You are not in the top ' . self::TOP_LIMIT . ' yet'
                ],
                'message' => 'Your current stats retrieved successfully'
            ];
        }

        return [
            'success' => true,
            'data' => [
                'user_uid' => $userEntry->user_uid,
                'rank' => $userEntry->rank,
                'total_points' => $userEntry->total_points,
                'claims_count' => $userEntry->claims_count,
                'ads_count' => $userEntry->ads_count,
                'is_in_top' => true,
                'user' => $userEntry->user
            ],
            'message' => 'Your rank retrieved successfully'
        ];
    }

    /**
     * Calculate current user points for this month
     */
    private function calculateCurrentUserPoints(string $userUid): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $claimsCount = DailyLoginClaim::where('user_uid', $userUid)
            ->whereBetween('claimed_at', [$startOfMonth, $endOfMonth])
            ->count();

        $adsCount = AdWatch::where('user_uid', $userUid)
            ->whereBetween('watched_at', [$startOfMonth, $endOfMonth])
            ->count();

        $totalPoints = ($claimsCount * self::POINTS_PER_CLAIM) + ($adsCount * self::POINTS_PER_AD_WATCH);

        return [
            'claims_count' => $claimsCount,
            'ads_count' => $adsCount,
            'total_points' => $totalPoints,
        ];
    }

    /**
     * Add points when user claims daily login
     */
    public function addClaimPoints(string $userUid): void
    {
        $this->updateUserLeaderboard($userUid);
    }

    /**
     * Add points when user watches ad
     */
    public function addAdWatchPoints(string $userUid): void
    {
        $this->updateUserLeaderboard($userUid);
    }

    /**
     * Update user's leaderboard entry
     */
    private function updateUserLeaderboard(string $userUid): void
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $currentPoints = $this->calculateCurrentUserPoints($userUid);

        // Update or create leaderboard entry
        $leaderboard = Leaderboard::where('user_uid', $userUid)->first();

        if ($leaderboard) {
            $leaderboard->update([
                'total_points' => $currentPoints['total_points'],
                'claims_count' => $currentPoints['claims_count'],
                'ads_count' => $currentPoints['ads_count'],
            ]);
        } else {
            $user = User::where('uid', $userUid)->first();
            if ($user && $currentPoints['total_points'] > 0) {
                Leaderboard::create([
                    'user_uid' => $userUid,
                    'user_id' => $user->id,
                    'total_points' => $currentPoints['total_points'],
                    'claims_count' => $currentPoints['claims_count'],
                    'ads_count' => $currentPoints['ads_count'],
                    'month_year' => $currentMonth,
                    'rank' => 999, // Temporary rank, will be recalculated
                ]);
            }
        }

        // Recalculate ranks for all users
        $this->recalculateRanks();
    }

    /**
     * Recalculate ranks for all leaderboard entries
     */
    private function recalculateRanks(): void
    {
        $leaderboardEntries = Leaderboard::orderBy('total_points', 'desc')->get();

        foreach ($leaderboardEntries as $index => $entry) {
            $entry->update(['rank' => $index + 1]);
        }
    }

    /**
     * Check if it's a new month and reset leaderboard
     */
    public function checkAndResetMonthlyLeaderboard(): void
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $lastEntry = Leaderboard::orderBy('created_at', 'desc')->first();

        if (!$lastEntry || $lastEntry->month_year !== $currentMonth) {
            $this->calculateMonthlyLeaderboard();
        }
    }
}