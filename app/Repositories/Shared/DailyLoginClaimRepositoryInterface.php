<?php

namespace App\Repositories\Shared;

interface DailyLoginClaimRepositoryInterface
{
    /**
     * Get all claims for a user
     */
    public function getByUserUid(string $userUid);

    /**
     * Get today's claim for a user
     */
    public function getTodayClaim(string $userUid);

    /**
     * Get claims by cycle number
     */
    public function getClaimsByCycle(string $userUid, int $cycleNumber);

    /**
     * Check if user has claimed today
     */
    public function hasClaimedToday(string $userUid): bool;

    /**
     * Create a new claim
     */
    public function create(array $data);

    /**
     * Get claim history with pagination
     */
    public function getHistory(string $userUid, int $perPage = 15);

    /**
     * Get total claims count for user
     */
    public function getTotalClaims(string $userUid): int;

    /**
     * Delete all claims for a user
     */
    public function deleteByUserUid(string $userUid): bool;
}
