<?php

namespace App\Repositories\Shared;

use App\Models\UserProgress;
use Carbon\Carbon;

class UserProgressRepository implements UserProgressRepositoryInterface
{
    protected $model;

    public function __construct(UserProgress $model)
    {
        $this->model = $model;
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
        $progress = $this->model->where('uid', $uid)->first();
        if ($progress) {
            $progress->update($data);
            return $progress;
        }
        return null;
    }

    public function updateScore(string $userUid, string $scoreType, float $score)
    {
        $progress = $this->findByUserUid($userUid);
        if ($progress) {
            $progress->update([
                $scoreType => $score,
                'last_update_date' => Carbon::today(),
            ]);
            return $progress;
        }
        return null;
    }

    public function updateJlptProgress(string $userUid, string $level, float $progress)
    {
        $userProgress = $this->findByUserUid($userUid);
        if ($userProgress) {
            $field = strtolower($level) . '_progress';
            $userProgress->update([
                $field => $progress,
                'last_update_date' => Carbon::today(),
            ]);
            return $userProgress;
        }
        return null;
    }

    public function incrementTodayLessons(string $userUid)
    {
        $progress = $this->findByUserUid($userUid);
        if ($progress) {
            // Check if need to reset daily lessons
            $lastUpdate = $progress->last_update_date ? Carbon::parse($progress->last_update_date) : null;
            $today = Carbon::today();

            if (!$lastUpdate || !$lastUpdate->isSameDay($today)) {
                // New day - move today to yesterday
                $progress->update([
                    'yesterday_lessons' => $progress->today_lessons,
                    'today_lessons' => 1,
                    'last_update_date' => $today,
                ]);
            } else {
                // Same day - increment
                $progress->increment('today_lessons');
            }
            return $progress->fresh();
        }
        return null;
    }

    public function resetDailyLessons(string $userUid)
    {
        $progress = $this->findByUserUid($userUid);
        if ($progress) {
            $progress->update([
                'yesterday_lessons' => $progress->today_lessons,
                'today_lessons' => 0,
                'last_update_date' => Carbon::today(),
            ]);
            return $progress;
        }
        return null;
    }

    public function getAllPaginated(int $perPage = 15)
    {
        return $this->model->with('user')->paginate($perPage);
    }

    public function deleteByUserUid(string $userUid): bool
    {
        return $this->model->where('user_uid', $userUid)->delete();
    }
}
