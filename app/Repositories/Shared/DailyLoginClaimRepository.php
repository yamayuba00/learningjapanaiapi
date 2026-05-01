<?php

namespace App\Repositories\Shared;

use App\Models\DailyLoginClaim;
use Carbon\Carbon;

class DailyLoginClaimRepository implements DailyLoginClaimRepositoryInterface
{
    protected $model;

    public function __construct(DailyLoginClaim $model)
    {
        $this->model = $model;
    }

    /**
     * Get all claims for a user
     */
    public function getByUserUid(string $userUid)
    {
        return $this->model->where('user_uid', $userUid)
            ->orderBy('claimed_at', 'desc')
            ->get();
    }

    /**
     * Get today's claim for a user
     */
    public function getTodayClaim(string $userUid)
    {
        $today = Carbon::today();
        
        return $this->model->where('user_uid', $userUid)
            ->whereDate('claimed_at', $today)
            ->first();
    }

    /**
     * Get claims by cycle number
     */
    public function getClaimsByCycle(string $userUid, int $cycleNumber)
    {
        return $this->model->where('user_uid', $userUid)
            ->where('cycle_number', $cycleNumber)
            ->orderBy('day_index', 'asc')
            ->get();
    }

    /**
     * Check if user has claimed today
     */
    public function hasClaimedToday(string $userUid): bool
    {
        return $this->getTodayClaim($userUid) !== null;
    }

    /**
     * Create a new claim
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Get claim history with pagination
     */
    public function getHistory(string $userUid, int $perPage = 15)
    {
        return $this->model->where('user_uid', $userUid)
            ->orderBy('claimed_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get total claims count for user
     */
    public function getTotalClaims(string $userUid): int
    {
        return $this->model->where('user_uid', $userUid)->count();
    }

    /**
     * Delete all claims for a user
     */
    public function deleteByUserUid(string $userUid): bool
    {
        return $this->model->where('user_uid', $userUid)->delete();
    }
}
