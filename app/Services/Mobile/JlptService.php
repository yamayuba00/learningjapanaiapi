<?php

namespace App\Services\Mobile;

use App\Repositories\Shared\JlptRepositoryInterface;
use App\Repositories\Shared\UserProgressRepositoryInterface;
use Carbon\Carbon;

class JlptService
{
    protected $jlptRepository;
    protected $progressRepository;

    // Total lessons per level (can be configured)
    protected $totalLessonsPerLevel = [
        'N5' => 20,
        'N4' => 25,
        'N3' => 30,
        'N2' => 35,
        'N1' => 40,
    ];

    public function __construct(
        JlptRepositoryInterface $jlptRepository,
        UserProgressRepositoryInterface $progressRepository
    ) {
        $this->jlptRepository = $jlptRepository;
        $this->progressRepository = $progressRepository;
    }

    /**
     * Get lessons progress for a level
     */
    public function getLessonsProgress(string $userUid, string $level): array
    {
        $lessons = $this->jlptRepository->getLessonsByLevel($userUid, $level);
        $completedCount = $this->jlptRepository->getCompletedLessonsCount($userUid, $level);
        $totalLessons = $this->totalLessonsPerLevel[$level] ?? 20;

        $progress = $totalLessons > 0 ? ($completedCount / $totalLessons) * 100 : 0;

        return [
            'level' => $level,
            'completed' => $completedCount,
            'total' => $totalLessons,
            'progress' => round($progress, 2),
            'lessons' => $lessons,
        ];
    }

    /**
     * Complete a lesson and update progress
     */
    public function completeLesson(string $userUid, string $level, int $lessonIndex): array
    {
        $lesson = $this->jlptRepository->completeLesson($userUid, $level, $lessonIndex);

        // Update user progress
        $completedCount = $this->jlptRepository->getCompletedLessonsCount($userUid, $level);
        $totalLessons = $this->totalLessonsPerLevel[$level] ?? 20;
        $progress = ($completedCount / $totalLessons) * 100;

        $this->progressRepository->updateJlptProgress($userUid, $level, round($progress, 2));

        return [
            'success' => true,
            'lesson' => $lesson,
            'progress' => round($progress, 2),
            'completed_count' => $completedCount,
        ];
    }

    /**
     * Submit test score
     */
    public function submitTestScore(string $userUid, array $testData): array
    {
        $score = ($testData['correct_answers'] / $testData['total_questions']) * 100;

        $testScore = $this->jlptRepository->saveTestScore([
            'user_uid' => $userUid,
            'level' => $testData['level'],
            'test_type' => $testData['test_type'],
            'score' => round($score, 2),
            'total_questions' => $testData['total_questions'],
            'correct_answers' => $testData['correct_answers'],
            'taken_at' => Carbon::now(),
        ]);

        // Check if passed (score >= 60%)
        $passed = $score >= 60;

        return [
            'success' => true,
            'test_score' => $testScore,
            'passed' => $passed,
            'score' => round($score, 2),
        ];
    }

    /**
     * Get test history for user
     */
    public function getTestHistory(string $userUid, ?string $level = null, ?string $testType = null): array
    {
        if ($level) {
            $scores = $this->jlptRepository->getTestScoresByLevel($userUid, $level);
        } elseif ($testType) {
            $scores = $this->jlptRepository->getTestScoresByType($userUid, $testType);
        } else {
            $scores = $this->jlptRepository->getAllUserTestScores($userUid);
        }

        return [
            'total_tests' => $scores->count(),
            'tests' => $scores,
        ];
    }

    /**
     * Get best scores for all levels
     */
    public function getBestScores(string $userUid): array
    {
        $levels = ['N5', 'N4', 'N3', 'N2', 'N1'];
        $testTypes = ['pretest', 'exam'];
        $bestScores = [];

        foreach ($levels as $level) {
            $bestScores[$level] = [];
            foreach ($testTypes as $testType) {
                $best = $this->jlptRepository->getBestScore($userUid, $level, $testType);
                $bestScores[$level][$testType] = $best ? [
                    'score' => $best->score,
                    'taken_at' => $best->taken_at,
                ] : null;
            }
        }

        return $bestScores;
    }
}
