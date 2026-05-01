<?php

namespace App\Repositories\Shared;

use App\Models\JlptLesson;
use App\Models\JlptTestScore;
use Carbon\Carbon;

class JlptRepository implements JlptRepositoryInterface
{
    protected $lessonModel;
    protected $testScoreModel;

    public function __construct(JlptLesson $lessonModel, JlptTestScore $testScoreModel)
    {
        $this->lessonModel = $lessonModel;
        $this->testScoreModel = $testScoreModel;
    }

    // ========== JLPT Lessons ==========

    public function getLessonsByLevel(string $userUid, string $level)
    {
        return $this->lessonModel
            ->where('user_uid', $userUid)
            ->where('level', $level)
            ->orderBy('lesson_index', 'asc')
            ->get();
    }

    public function getLesson(string $userUid, string $level, int $lessonIndex)
    {
        return $this->lessonModel
            ->where('user_uid', $userUid)
            ->where('level', $level)
            ->where('lesson_index', $lessonIndex)
            ->first();
    }

    public function completeLesson(string $userUid, string $level, int $lessonIndex)
    {
        $lesson = $this->getLesson($userUid, $level, $lessonIndex);

        if ($lesson) {
            $lesson->update([
                'is_completed' => true,
                'completed_at' => Carbon::now(),
            ]);
            return $lesson;
        }

        // Create if not exists
        return $this->lessonModel->create([
            'user_uid' => $userUid,
            'level' => $level,
            'lesson_index' => $lessonIndex,
            'is_completed' => true,
            'completed_at' => Carbon::now(),
        ]);
    }

    public function getCompletedLessonsCount(string $userUid, string $level): int
    {
        return $this->lessonModel
            ->where('user_uid', $userUid)
            ->where('level', $level)
            ->where('is_completed', true)
            ->count();
    }

    public function getAllUserLessons(string $userUid)
    {
        return $this->lessonModel
            ->where('user_uid', $userUid)
            ->orderBy('level', 'desc')
            ->orderBy('lesson_index', 'asc')
            ->get();
    }

    // ========== JLPT Test Scores ==========

    public function saveTestScore(array $data)
    {
        return $this->testScoreModel->create($data);
    }

    public function getTestScoresByLevel(string $userUid, string $level)
    {
        return $this->testScoreModel
            ->where('user_uid', $userUid)
            ->where('level', $level)
            ->orderBy('taken_at', 'desc')
            ->get();
    }

    public function getTestScoresByType(string $userUid, string $testType)
    {
        return $this->testScoreModel
            ->where('user_uid', $userUid)
            ->where('test_type', $testType)
            ->orderBy('taken_at', 'desc')
            ->get();
    }

    public function getBestScore(string $userUid, string $level, string $testType)
    {
        return $this->testScoreModel
            ->where('user_uid', $userUid)
            ->where('level', $level)
            ->where('test_type', $testType)
            ->orderBy('score', 'desc')
            ->first();
    }

    public function getAllUserTestScores(string $userUid)
    {
        return $this->testScoreModel
            ->where('user_uid', $userUid)
            ->orderBy('taken_at', 'desc')
            ->get();
    }

    public function getLatestTestScore(string $userUid, string $level, string $testType)
    {
        return $this->testScoreModel
            ->where('user_uid', $userUid)
            ->where('level', $level)
            ->where('test_type', $testType)
            ->orderBy('taken_at', 'desc')
            ->first();
    }
}
