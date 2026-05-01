<?php

namespace App\Repositories\Mobile;

use App\Models\UserCredit;

/**
 * User Credit Repository for Mobile App
 * Read-only access to user's own credits
 */
class UserCreditRepository implements UserCreditRepositoryInterface
{
    protected $model;

    public function __construct(UserCredit $model)
    {
        $this->model = $model;
    }

    public function findByUserUid(string $userUid)
    {
        return $this->model->where('user_uid', $userUid)->first();
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

    public function getUserRank(string $userUid)
    {
        $credit = $this->findByUserUid($userUid);

        if (!$credit) {
            return null;
        }

        $rank = $this->model->where('total_points', '>', $credit->total_points)->count() + 1;

        return [
            'rank' => $rank,
            'total_points' => $credit->total_points,
        ];
    }
}
