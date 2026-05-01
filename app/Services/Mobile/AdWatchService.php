<?php

namespace App\Services\Mobile;

use App\Repositories\Shared\AdWatchRepositoryInterface;
use App\Repositories\Shared\UserCreditRepositoryInterface;
use App\Services\Mobile\LeaderboardService;
use Carbon\Carbon;

class AdWatchService
{
    protected $adWatchRepository;
    protected $creditRepository;
    protected $leaderboardService;

    // Ad rewards configuration
    protected $adRewards = [
        'premium' => 5,  // Premium ad gives 5 credits
        'regular' => 2,  // Regular ad gives 2 credits
    ];

    // Daily limits
    protected $dailyLimits = [
        'premium' => 1,  // Max 1 premium ad per day
        'regular' => 3,  // Max 3 regular ads per day
    ];

    public function __construct(
        AdWatchRepositoryInterface $adWatchRepository,
        UserCreditRepositoryInterface $creditRepository,
        LeaderboardService $leaderboardService
    ) {
        $this->adWatchRepository = $adWatchRepository;
        $this->creditRepository = $creditRepository;
        $this->leaderboardService = $leaderboardService;
    }

    /**
     * Check if user can watch ad
     */
    public function canWatchAd(string $userUid, string $adType): array
    {
        if (!in_array($adType, ['premium', 'regular'])) {
            return [
                'can_watch' => false,
                'reason' => 'Invalid ad type',
            ];
        }

        $todayCount = $this->adWatchRepository->getTodayWatchesByType($userUid, $adType);
        $limit = $this->dailyLimits[$adType];

        if ($todayCount >= $limit) {
            return [
                'can_watch' => false,
                'reason' => 'Daily limit reached for ' . $adType . ' ads',
                'watched_today' => $todayCount,
                'daily_limit' => $limit,
            ];
        }

        return [
            'can_watch' => true,
            'reward' => $this->adRewards[$adType],
            'watched_today' => $todayCount,
            'remaining' => $limit - $todayCount,
        ];
    }

    /**
     * Record ad watch and give reward
     */
    public function watchAd(string $userUid, string $adType): array
    {
        $canWatch = $this->canWatchAd($userUid, $adType);

        if (!$canWatch['can_watch']) {
            return [
                'success' => false,
                'message' => $canWatch['reason'],
                'details' => $canWatch,
            ];
        }

        $reward = $this->adRewards[$adType];

        // Record ad watch
        $adWatch = $this->adWatchRepository->create([
            'user_uid' => $userUid,
            'ad_type' => $adType,
            'credits_earned' => $reward,
            'watched_at' => Carbon::now(),
            'watch_date' => Carbon::today(),
        ]);

        // Add credits to user
        $this->creditRepository->addCredits($userUid, $reward);

        // Add points to leaderboard
        $this->leaderboardService->addAdWatchPoints($userUid);

        return [
            'success' => true,
            'message' => 'Ad watched successfully',
            'ad_watch' => $adWatch,
            'credits_earned' => $reward,
            'remaining_today' => $canWatch['remaining'] - 1,
        ];
    }

    /**
     * Get today's ad watch status
     */
    public function getTodayStatus(string $userUid): array
    {
        $todayWatches = $this->adWatchRepository->getTodayWatches($userUid);
        
        $premiumCount = $todayWatches->where('ad_type', 'premium')->count();
        $regularCount = $todayWatches->where('ad_type', 'regular')->count();

        $totalEarned = $todayWatches->sum('credits_earned');

        return [
            'premium' => [
                'watched' => $premiumCount,
                'limit' => $this->dailyLimits['premium'],
                'remaining' => max(0, $this->dailyLimits['premium'] - $premiumCount),
                'reward_per_ad' => $this->adRewards['premium'],
            ],
            'regular' => [
                'watched' => $regularCount,
                'limit' => $this->dailyLimits['regular'],
                'remaining' => max(0, $this->dailyLimits['regular'] - $regularCount),
                'reward_per_ad' => $this->adRewards['regular'],
            ],
            'total_earned_today' => $totalEarned,
        ];
    }

    /**
     * Get ad watch history
     */
    public function getHistory(string $userUid, int $perPage = 15)
    {
        return $this->adWatchRepository->getPaginated($userUid, $perPage);
    }
}
