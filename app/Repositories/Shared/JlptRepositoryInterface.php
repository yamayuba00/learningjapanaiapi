<?php

namespace App\Repositories\Shared;

interface JlptRepositoryInterface
{
    // ========== JLPT Lessons ==========
    
    /**
     * Get user lessons by level
     */
    public function getLessonsByLevel(string $userUid, string $level);

    /**
     * Get specific lesson
     */
    public function getLesson(string $userUid, string $level, int $lessonIndex);

    /**
     * Mark lesson as completed
     */
    public function completeLesson(string $userUid, string $level, int $lessonIndex);

    /**
     * Get completed lessons count
     */
    public function getCompletedLessonsCount(string $userUid, string $level): int;

    /**
     * Get all user lessons
     */
    public function getAllUserLessons(string $userUid);

    // ========== JLPT Test Scores ==========
    
    /**
     * Save test score
     */
    public function saveTestScore(array $data);

    /**
     * Get user test scores by level
     */
    public function getTestScoresByLevel(string $userUid, string $level);

    /**
     * Get user test scores by type
     */
    public function getTestScoresByType(string $userUid, string $testType);

    /**
     * Get best score for level
     */
    public function getBestScore(string $userUid, string $level, string $testType);

    /**
     * Get all user test scores
     */
    public function getAllUserTestScores(string $userUid);

    /**
     * Get latest test score
     */
    public function getLatestTestScore(string $userUid, string $level, string $testType);
}
