<?php

namespace App\Repositories\Mobile;

/**
 * User Credit Repository Interface for Mobile App
 * Handles user's own credit operations (read-only)
 */
interface UserCreditRepositoryInterface
{
    /**
     * Get user credit by user UID
     */
    public function findByUserUid(string $userUid);

    /**
     * Get cycle information
     */
    public function getCycleInfo(string $userUid);

    /**
     * Get user's rank by points
     */
    public function getUserRank(string $userUid);
}
