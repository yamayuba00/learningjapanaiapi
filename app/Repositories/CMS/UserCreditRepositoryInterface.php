<?php

namespace App\Repositories\CMS;

/**
 * User Credit Repository Interface for CMS Admin
 * Full CRUD access to all users' credits
 */
interface UserCreditRepositoryInterface
{
    /**
     * Get all credits with pagination
     */
    public function getAllPaginated(int $perPage = 15);

    /**
     * Find credit by user UID
     */
    public function findByUserUid(string $userUid);

    /**
     * Find credit by UID
     */
    public function findByUid(string $uid);

    /**
     * Create new credit
     */
    public function create(array $data);

    /**
     * Update credit
     */
    public function update(string $uid, array $data);

    /**
     * Add credits to user
     */
    public function addCredits(string $userUid, int $amount);

    /**
     * Deduct credits from user
     */
    public function deductCredits(string $userUid, int $amount);

    /**
     * Add points to user
     */
    public function addPoints(string $userUid, int $amount);

    /**
     * Update streak
     */
    public function updateStreak(string $userUid, int $streak);

    /**
     * Reset cycle
     */
    public function resetCycle(string $userUid);

    /**
     * Get top users by points
     */
    public function getTopUsersByPoints(int $limit = 10);

    /**
     * Get cycle info
     */
    public function getCycleInfo(string $userUid);

    /**
     * Delete credit
     */
    public function delete(string $uid): bool;
}
