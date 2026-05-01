<?php

namespace App\Repositories\Shared;

/**
 * Shared User Credit Repository Interface
 * Contains all methods that can be used by both Mobile and CMS
 */
interface UserCreditRepositoryInterface
{
    public function findByUid(string $uid);
    public function findByUserUid(string $userUid);
    public function create(array $data);
    public function update(string $uid, array $data);
    public function addCredits(string $userUid, int $amount);
    public function deductCredits(string $userUid, int $amount);
    public function addPoints(string $userUid, int $amount);
    public function updateStreak(string $userUid, int $streak);
    public function resetCycle(string $userUid);
    public function getCycleInfo(string $userUid);
}
