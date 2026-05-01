<?php

namespace App\Repositories\Shared;

use App\Models\AdWatch;
use Carbon\Carbon;

class AdWatchRepository implements AdWatchRepositoryInterface
{
    protected $model;

    public function __construct(AdWatch $model)
    {
        $this->model = $model;
    }

    public function findByUserUid(string $userUid)
    {
        return $this->model->where('user_uid', $userUid)
            ->orderBy('watched_at', 'desc')
            ->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function getTodayWatches(string $userUid)
    {
        return $this->model->where('user_uid', $userUid)
            ->whereDate('watch_date', Carbon::today())
            ->get();
    }

    public function getTodayWatchesByType(string $userUid, string $adType): int
    {
        return $this->model->where('user_uid', $userUid)
            ->where('ad_type', $adType)
            ->whereDate('watch_date', Carbon::today())
            ->count();
    }

    public function getPaginated(string $userUid, int $perPage = 15)
    {
        return $this->model->where('user_uid', $userUid)
            ->orderBy('watched_at', 'desc')
            ->paginate($perPage);
    }
}
