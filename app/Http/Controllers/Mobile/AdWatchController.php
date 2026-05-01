<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Mobile\AdWatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Ad Watch Controller for Mobile App
 */
class AdWatchController extends Controller
{
    protected $adWatchService;

    public function __construct(AdWatchService $adWatchService)
    {
        $this->adWatchService = $adWatchService;
    }

    /**
     * Get today's ad watch status
     */
    public function status(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $status = $this->adWatchService->getTodayStatus($userUid);

            return ResponseHelper::success($status, 'Ad watch status retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get status: ' . $e->getMessage());
        }
    }

    /**
     * Watch ad and get reward
     */
    public function watch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ad_type' => 'required|in:premium,regular',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $userUid = $request->user()->uid;
            $result = $this->adWatchService->watchAd($userUid, $request->ad_type);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success($result, $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to watch ad: ' . $e->getMessage());
        }
    }

    /**
     * Check if can watch ad
     */
    public function canWatch(Request $request, $adType)
    {
        try {
            $userUid = $request->user()->uid;
            $canWatch = $this->adWatchService->canWatchAd($userUid, $adType);

            return ResponseHelper::success($canWatch, 'Check completed successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to check: ' . $e->getMessage());
        }
    }

    /**
     * Get ad watch history
     */
    public function history(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $perPage = $request->input('per_page', 15);
            
            $history = $this->adWatchService->getHistory($userUid, $perPage);

            return ResponseHelper::success($history, 'Ad watch history retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get history: ' . $e->getMessage());
        }
    }
}
