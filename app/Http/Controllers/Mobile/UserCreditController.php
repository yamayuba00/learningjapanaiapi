<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Shared\UserCreditRepositoryInterface;
use Illuminate\Http\Request;

/**
 * User Credit Controller for Mobile App
 * Handles user's own credit operations
 */
class UserCreditController extends Controller
{
    protected $creditRepository;

    public function __construct(UserCreditRepositoryInterface $creditRepository)
    {
        $this->creditRepository = $creditRepository;
    }

    /**
     * Get my credits
     */
    public function myCredit(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $credit = $this->creditRepository->findByUserUid($userUid);

            if (!$credit) {
                return ResponseHelper::notFound('Credit not found');
            }

            return ResponseHelper::success($credit, 'Credit retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get credit: ' . $e->getMessage());
        }
    }

    /**
     * Get my credit balance only
     */
    public function balance(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $credit = $this->creditRepository->findByUserUid($userUid);

            if (!$credit) {
                return ResponseHelper::notFound('Credit not found');
            }

            return ResponseHelper::success([
                'credits' => $credit->credits,
                'total_points' => $credit->total_points,
            ], 'Balance retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get balance: ' . $e->getMessage());
        }
    }

    /**
     * Get my streak info
     */
    public function streak(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $credit = $this->creditRepository->findByUserUid($userUid);

            if (!$credit) {
                return ResponseHelper::notFound('Credit not found');
            }

            return ResponseHelper::success([
                'streak' => $credit->streak,
                'cycle_number' => $credit->cycle_number,
                'cycle_start_date' => $credit->cycle_start_date,
                'last_claim_date' => $credit->last_claim_date,
            ], 'Streak info retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get streak info: ' . $e->getMessage());
        }
    }

    /**
     * Get cycle information
     */
    public function cycleInfo(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $cycleInfo = $this->creditRepository->getCycleInfo($userUid);

            return ResponseHelper::success($cycleInfo, 'Cycle info retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get cycle info: ' . $e->getMessage());
        }
    }
}
