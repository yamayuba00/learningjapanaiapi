<?php

namespace App\Services\CMS;

use App\Repositories\Shared\UserCreditRepositoryInterface;
use App\Repositories\Shared\DailyLoginClaimRepositoryInterface;
use Carbon\Carbon;

/**
 * Daily Login Service for CMS
 * Handles daily login rewards management for admins
 */
class DailyLoginService
{
    protected $claimRepository;
    protected $creditRepository;

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
        UserCreditRepositoryInterface $creditRepository
    ) {
        $this->claimRepository = $claimRepository;
        $this->creditRepository = $creditRepository;
    }

    /**
     * Get claim status for any user (admin view)
     */
    public function getClaimStatus(string $userUid): array
    {
        $userCredit = $this->creditRepository->findByUserUid($userUid);
        
        if (!$userCredit) {
            return [
                'success' => false,
                'message' => 'User credit not found',
            ];
        }

        $currentCycle = $userCredit->cycle_number ?? 1;
        $cycleClaims = $this->claimRepository->getClaimsByCycle($userUid, $currentCycle);
        $hasClaimedToday = $this->claimRepository->hasClaimedToday($userUid);

        return [
            'success' => true,
            'has_claimed_today' => $hasClaimedToday,
            'current_cycle' => $currentCycle,
            'current_day' => $cycleClaims->count(),
            'cycle_progress' => $cycleClaims->count() . '/7',
            'streak' => $userCredit->streak ?? 0,
            'last_claim_date' => $userCredit->last_claim_date,
            'cycle_claims' => $cycleClaims,
        ];
    }

    /**
     * Get claim history for any user (admin view)
     */
    public function getHistory(string $userUid, int $perPage = 15)
    {
        return $this->claimRepository->getHistory($userUid, $perPage);
    }

    /**
     * Get all claims with pagination (admin view)
     */
    public function getAllClaims(int $perPage = 15)
    {
        // This would need a new repository method
        // For now, return empty
        return [];
    }

    /**
     * Reset user's daily login cycle (admin action)
     */
    public function resetUserCycle(string $userUid): array
    {
        $userCredit = $this->creditRepository->findByUserUid($userUid);
        
        if (!$userCredit) {
            return [
                'success' => false,
                'message' => 'User credit not found',
            ];
        }

        $this->creditRepository->update($userCredit->uid, [
            'cycle_number' => 1,
            'cycle_start_date' => Carbon::today(),
            'last_claim_date' => null,
            'streak' => 0,
        ]);

        return [
            'success' => true,
            'message' => 'User daily login cycle reset successfully',
        ];
    }

    /**
     * Delete claim history for user (admin action)
     */
    public function deleteUserHistory(string $userUid): array
    {
        $deleted = $this->claimRepository->deleteByUserUid($userUid);

        return [
            'success' => $deleted,
            'message' => $deleted ? 'User claim history deleted successfully' : 'Failed to delete claim history',
        ];
    }
}
