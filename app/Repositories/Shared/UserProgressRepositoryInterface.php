<?php

namespace App\Repositories\Shared;

interface UserProgressRepositoryInterface
{
    /**
     * Get user progress by user UID
     */
    public function findByUserUid(string $userUid);

    /**
     * Create user progress
     */
    public function create(array $data);

    /**
     * Update user progress
     */
    public function update(string $uid, array $data);

    /**
     * Update specific score
     */
    public function updateScore(string $userUid, string $scoreType, float $score);

    /**
     * Update JLPT level progress
     */
    public function updateJlptProgress(string $userUid, string $level, float $progress);

    /**
     * Increment today lessons
     */
    public function incrementTodayLessons(string $userUid);

    /**
     * Reset daily lessons (move today to yesterday)
     */
    public function resetDailyLessons(string $userUid);

    /**
     * Get all progress (paginated)
     */
    public function getAllPaginated(int $perPage = 15);

    /**
     * Delete user progress
     */
    public function deleteByUserUid(string $userUid): bool;
}
