<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Mobile\JlptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * JLPT Controller for Mobile App
 */
class JlptController extends Controller
{
    protected $jlptService;

    public function __construct(JlptService $jlptService)
    {
        $this->jlptService = $jlptService;
    }

    /**
     * Get lessons for a level
     */
    public function getLessons(Request $request, $level)
    {
        try {
            $userUid = $request->user()->uid;
            $progress = $this->jlptService->getLessonsProgress($userUid, strtoupper($level));

            return ResponseHelper::success($progress, 'Lessons retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get lessons: ' . $e->getMessage());
        }
    }

    /**
     * Complete a lesson
     */
    public function completeLesson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|in:N5,N4,N3,N2,N1',
            'lesson_index' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $userUid = $request->user()->uid;
            $result = $this->jlptService->completeLesson(
                $userUid,
                $request->level,
                $request->lesson_index
            );

            return ResponseHelper::success($result, 'Lesson completed successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to complete lesson: ' . $e->getMessage());
        }
    }

    /**
     * Submit test score
     */
    public function submitTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|in:N5,N4,N3,N2,N1',
            'test_type' => 'required|in:pretest,exam',
            'total_questions' => 'required|integer|min:1',
            'correct_answers' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $userUid = $request->user()->uid;
            $result = $this->jlptService->submitTestScore($userUid, $request->all());

            return ResponseHelper::success($result, 'Test score submitted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to submit test: ' . $e->getMessage());
        }
    }

    /**
     * Get test history
     */
    public function getTestHistory(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $level = $request->input('level');
            $testType = $request->input('test_type');

            $history = $this->jlptService->getTestHistory($userUid, $level, $testType);

            return ResponseHelper::success($history, 'Test history retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get test history: ' . $e->getMessage());
        }
    }

    /**
     * Get best scores
     */
    public function getBestScores(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $bestScores = $this->jlptService->getBestScores($userUid);

            return ResponseHelper::success($bestScores, 'Best scores retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get best scores: ' . $e->getMessage());
        }
    }
}
