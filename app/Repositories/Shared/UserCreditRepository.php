<?php

namespace App\Repositories\Shared;

use App\Models\UserCredit;
use Carbon\Carbon;

/**
 * Shared User Credit Repository
 * Base implementation used by both Mobile and CMS
 */
class UserCreditRepository implements UserCreditRepositoryInterface
{
    protected $model;

    public function __construct(UserCredit $model)
    {
        $this->model = $model;
    }

    public function findByUid(string $uid)
    {
        return $this->model->where('uid', $uid)->first();
    }

    public function findByUserUid(string $userUid)
    {
        return $this->model->where('user_uid', $userUid)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $uid, array $data)
    {
        $credit = $this->findByUid($uid);
        if ($credit) {
            $credit->update($data);
            return $credit;
        }
        return null;
    }

    public function addCredits(string $userUid, int $amount)
    {
        $credit = $this->findByUserUid($userUid);
        if ($credit) {
            $credit->increment('credits', $amount);
            return true;
        }
        return false;
    }

    public function deductCredits(string $userUid, int $amount)
    {
        $credit = $this->findByUserUid($userUid);
        if ($credit && $credit->credits >= $amount) {
            $credit->decrement('credits', $amount);
            return true;
        }
        return false;
    }

    public function addPoints(string $userUid, int $amount)
    {
        $credit = $this->findByUserUid($userUid);
        if ($credit) {
            $credit->increment('total_points', $amount);
            return true;
        }
        return false;
    }

    public function updateStreak(string $userUid, int $streak)
    {
        $credit = $this->findByUserUid($userUid);
        if ($credit) {
            $credit->update(['streak' => $streak]);
            return true;
        }
        return false;
    }

    public function resetCycle(string $userUid)
    {
        $credit = $this->findByUserUid($userUid);
        if ($credit) {
            $credit->update([
                'cycle_number' => $credit->cycle_number + 1,
                'cycle_start_date' => Carbon::today(),
                'last_claim_date' => null,
            ]);
            return true;
        }
        return false;
    }

    public function getCycleInfo(string $userUid)
    {
        $credit = $this->findByUserUid($userUid);
        if (!$credit) {
            return null;
        }

        return [
            'cycle_number' => $credit->cycle_number,
            'cycle_start_date' => $credit->cycle_start_date,
            'last_claim_date' => $credit->last_claim_date,
            'streak' => $credit->streak,
        ];
    }

    public function getAllPaginated(int $perPage = 15)
    {
        return $this->model->with('user')->paginate($perPage);
    }

    public function getTopUsersByPoints(int $limit = 10)
    {
        return $this->model->with('user')
            ->orderBy('total_points', 'desc')
            ->limit($limit)
            ->get();
    }
}
