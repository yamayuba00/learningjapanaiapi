<?php

namespace App\Http\Controllers\CMS;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Shared\UserProgressRepositoryInterface;
use App\Services\Mobile\UserProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * User Progress Controller for CMS (Admin)
 */
class UserProgressController extends Controller
{
    protected $progressService;
    protected $progressRepository;

    public function __construct(
        UserProgressService $progressService,
        UserProgressRepositoryInterface $progressRepository
    ) {
        $this->progressService = $progressService;
        $this->progressRepository = $progressRepository;
    }

    /**
     * Get all user progress (paginated)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $progress = $this->progressRepository->getAllPaginated($perPage);

            return ResponseHelper::success($progress, 'All progress retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get progress: ' . $e->getMessage());
        }
    }

    /**
     * Get specific user progress
     */
    public function show($userUid)
    {
        try {
            $progress = $this->progressService->getOrCreateProgress($userUid);

            return ResponseHelper::success($progress, 'User progress retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get user progress: ' . $e->getMessage());
        }
    }

    /**
     * Update user progress
     */
    public function update(Request $request, $userUid)
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
            'today_lessons' => 'nullable|integer|min:0',
            'yesterday_lessons' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $result = $this->progressService->updateScores($userUid, $request->all());

            if (!$result['success']) {
                return ResponseHelper::error('Failed to update progress');
            }

            return ResponseHelper::success($result['progress'], 'User progress updated successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to update progress: ' . $e->getMessage());
        }
    }

    /**
     * Reset user daily lessons
     */
    public function resetDailyLessons($userUid)
    {
        try {
            $progress = $this->progressRepository->resetDailyLessons($userUid);

            if (!$progress) {
                return ResponseHelper::error('Failed to reset daily lessons');
            }

            return ResponseHelper::success($progress, 'Daily lessons reset successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to reset daily lessons: ' . $e->getMessage());
        }
    }
}
