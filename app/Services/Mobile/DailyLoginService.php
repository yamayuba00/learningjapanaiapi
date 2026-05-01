<?php

namespace App\Services\Mobile;

use App\Repositories\Shared\UserCreditRepositoryInterface;
use App\Repositories\Shared\DailyLoginClaimRepositoryInterface;
use App\Services\Mobile\LeaderboardService;
use Carbon\Carbon;

/**
 * Daily Login Service for Mobile App
 * Handles daily login rewards for users
 */
class DailyLoginService
{
    protected $claimRepository;
    protected $creditRepository;
    protected $leaderboardService;

    // Reward configuration (7-day cycle)
    protected $dailyRewards = [
        1 => ['credits' => 5, 'points' => 10],
        2 => ['credits' => 5, 'points' => 10],
        3 => ['credits' => 10, 'points' => 20],
        4 => ['credits' => 10, 'points' => 20],
        5 => ['credits' => 15, 'points' => 30],
        6 => ['credits' => 15, 'points' => 30],
        7 => ['credits' => 25, 'points' => 50],
    ];

    public function __construct(
        DailyLoginClaimRepositoryInterface $claimRepository,
        UserCreditRepositoryInterface $creditRepository,
        LeaderboardService $leaderboardService
    ) {
        $this->claimRepository = $claimRepository;
        $this->creditRepository = $creditRepository;
        $this->leaderboardService = $leaderboardService;
    }

    public function canClaimToday(string $userUid): array
    {
        $hasClaimedToday = $this->claimRepository->hasClaimedToday($userUid);
        $userCredit = $this->creditRepository->findByUserUid($userUid);

        if (!$userCredit) {
            return [
                'can_claim' => false,
                'message' => 'User credit not found',
            ];
        }

        if ($hasClaimedToday) {
            return [
                'can_claim' => false,
                'message' => 'Already claimed today',
                'next_claim_at' => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            ];
        }

        $lastClaimDate = $userCredit->last_claim_date;
        $today = Carbon::today();
        
        $dayIndex = 1;
        $cycleNumber = $userCredit->cycle_number ?? 1;

        if ($lastClaimDate) {
            $lastClaim = Carbon::parse($lastClaimDate);
            $daysDiff = $today->diffInDays($lastClaim);

            if ($daysDiff == 1) {
                $currentClaims = $this->claimRepository->getClaimsByCycle($userUid, $cycleNumber);
                $dayIndex = $currentClaims->count() + 1;

                if ($dayIndex > 7) {
                    $dayIndex = 1;
                    $cycleNumber++;
                }
            } else if ($daysDiff > 1) {
                $dayIndex = 1;
                $cycleNumber++;
            }
        }

        $reward = $this->dailyRewards[$dayIndex];

        return [
            'can_claim' => true,
            'day_index' => $dayIndex,
            'cycle_number' => $cycleNumber,
            'reward' => $reward,
            'message' => "You can claim Day {$dayIndex} reward",
        ];
    }

    public function claimDailyReward(string $userUid): array
    {
        $canClaim = $this->canClaimToday($userUid);

        if (!$canClaim['can_claim']) {
            return [
                'success' => false,
                'message' => $canClaim['message'],
            ];
        }

        $dayIndex = $canClaim['day_index'];
        $cycleNumber = $canClaim['cycle_number'];
        $reward = $canClaim['reward'];

        // Get user to get user_id
        $userCredit = $this->creditRepository->findByUserUid($userUid);
        if (!$userCredit) {
            return [
                'success' => false,
                'message' => 'User credit not found',
            ];
        }

        $claim = $this->claimRepository->create([
            'user_id' => $userCredit->user_id,
            'user_uid' => $userUid,
            'cycle_number' => $cycleNumber,
            'day_index' => $dayIndex,
            'points_earned' => $reward['points'],
            'credits_earned' => $reward['credits'],
            'claimed_at' => Carbon::now(),
        ]);

        $this->creditRepository->addCredits($userUid, $reward['credits']);
        $this->creditRepository->addPoints($userUid, $reward['points']);
        
        $this->creditRepository->update($userCredit->uid, [
            'last_claim_date' => Carbon::today(),
            'cycle_number' => $cycleNumber,
            'cycle_start_date' => $dayIndex == 1 ? Carbon::today() : $userCredit->cycle_start_date,
        ]);

        if ($dayIndex > 1) {
            $this->creditRepository->updateStreak($userUid, $userCredit->streak + 1);
        } else {
            $this->creditRepository->updateStreak($userUid, 1);
        }

        // Add points to leaderboard
        $this->leaderboardService->addClaimPoints($userUid);

        return [
            'success' => true,
            'message' => "Day {$dayIndex} reward claimed successfully!",
            'claim' => $claim,
            'reward' => $reward,
            'cycle_completed' => $dayIndex == 7,
        ];
    }

    public function getClaimStatus(string $userUid): array
    {
        $userCredit = $this->creditRepository->findByUserUid($userUid);
        $canClaim = $this->canClaimToday($userUid);
        $currentCycle = $userCredit->cycle_number ?? 1;
        $cycleClaims = $this->claimRepository->getClaimsByCycle($userUid, $currentCycle);

        return [
            'can_claim_today' => $canClaim['can_claim'],
            'current_cycle' => $currentCycle,
            'current_day' => $cycleClaims->count(),
            'cycle_progress' => $cycleClaims->count() . '/7',
            'streak' => $userCredit->streak ?? 0,
            'last_claim_date' => $userCredit->last_claim_date,
            'next_reward' => $canClaim['can_claim'] ? $canClaim['reward'] : null,
            'cycle_claims' => $cycleClaims,
        ];
    }

    public function getHistory(string $userUid, int $perPage = 15)
    {
        return $this->claimRepository->getHistory($userUid, $perPage);
    }
}
