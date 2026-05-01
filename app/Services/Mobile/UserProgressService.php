<?php

namespace App\Services\Mobile;

use App\Repositories\Shared\UserProgressRepositoryInterface;

class UserProgressService
{
    protected $progressRepository;

    public function __construct(UserProgressRepositoryInterface $progressRepository)
    {
        $this->progressRepository = $progressRepository;
    }

    /**
     * Get or create user progress
     */
    public function getOrCreateProgress(string $userUid)
    {
        $progress = $this->progressRepository->findByUserUid($userUid);
        
        if (!$progress) {
            $progress = $this->progressRepository->create([
                'user_uid' => $userUid,
            ]);
        }

        return $progress;
    }

    /**
     * Update multiple scores at once
     */
    public function updateScores(string $userUid, array $scores): array
    {
        $progress = $this->getOrCreateProgress($userUid);
        
        $updated = $this->progressRepository->update($progress->uid, $scores);

        return [
            'success' => $updated !== null,
            'progress' => $updated,
        ];
    }

    /**
     * Complete a lesson
     */
    public function completeLesson(string $userUid): array
    {
        $progress = $this->progressRepository->incrementTodayLessons($userUid);

        return [
            'success' => $progress !== null,
            'today_lessons' => $progress ? $progress->today_lessons : 0,
            'yesterday_lessons' => $progress ? $progress->yesterday_lessons : 0,
        ];
    }

    /**
     * Get progress summary
     */
    public function getProgressSummary(string $userUid): array
    {
        $progress = $this->getOrCreateProgress($userUid);

        return [
            'basic_skills' => [
                'hiragana' => $progress->hiragana_score,
                'katakana' => $progress->katakana_score,
                'vocabulary' => $progress->vocabulary_score,
            ],
            'jlpt_levels' => [
                'n5' => $progress->n5_progress,
                'n4' => $progress->n4_progress,
                'n3' => $progress->n3_progress,
                'n2' => $progress->n2_progress,
                'n1' => $progress->n1_progress,
            ],
            'daily_activity' => [
                'today' => $progress->today_lessons,
                'yesterday' => $progress->yesterday_lessons,
            ],
            'last_update' => $progress->last_update_date,
        ];
    }
}
