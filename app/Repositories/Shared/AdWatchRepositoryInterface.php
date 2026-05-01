<?php

namespace App\Repositories\Shared;

interface AdWatchRepositoryInterface
{
    public function findByUserUid(string $userUid);
    public function create(array $data);
    public function getTodayWatches(string $userUid);
    public function getTodayWatchesByType(string $userUid, string $adType): int;
    public function getPaginated(string $userUid, int $perPage = 15);
}
