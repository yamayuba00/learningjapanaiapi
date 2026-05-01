<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Mobile\UserProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * User Progress Controller for Mobile App
 */
class UserProgressController extends Controller
{
    protected $progressService;

    public function __construct(UserProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Get my progress
     */
    public function index(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $progress = $this->progressService->getOrCreateProgress($userUid);

            return ResponseHelper::success($progress, 'Progress retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get progress: ' . $e->getMessage());
        }
    }

    /**
     * Get progress summary
     */
    public function summary(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $summary = $this->progressService->getProgressSummary($userUid);

            return ResponseHelper::success($summary, 'Progress summary retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get progress summary: ' . $e->getMessage());
        }
    }

    /**
     * Update progress scores
     */
    public function updateScores(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hiragana_score' => 'nullable|numeric|min:0|max:100',
            'katakana_score' => 'nullable|numeric|min:0|max:100',
            'vocabulary_score' => 'nullable|numeric|min:0|max:100',
            'n5_progress' => 'nullable|numeric|min:0|max:100',
            'n4_progress' => 'nullable|numeric|min:0|max:100',
            'n3_progress' => 'nullable|numeric|min:0|max:100',
            'n2_progress' => 'nullable|numeric|min:0|max:100',
            'n1_progress' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $userUid = $request->user()->uid;
            $result = $this->progressService->updateScores($userUid, $request->all());

            if (!$result['success']) {
                return ResponseHelper::error('Failed to update progress');
            }

            return ResponseHelper::success($result['progress'], 'Progress updated successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to update progress: ' . $e->getMessage());
        }
    }

    /**
     * Mark lesson as completed
     */
    public function completeLesson(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $result = $this->progressService->completeLesson($userUid);

            return ResponseHelper::success($result, 'Lesson completed successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to complete lesson: ' . $e->getMessage());
        }
    }
}
