<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ReferralReward;
use App\Models\User;
use App\Repositories\Shared\UserCreditRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Referral Controller for Mobile App
 */
class ReferralController extends Controller
{
    protected $creditRepository;

    // Referral rewards
    protected $referrerReward = 100; // Credits for referrer
    protected $referredReward = 40;  // Credits for referred user

    public function __construct(UserCreditRepositoryInterface $creditRepository)
    {
        $this->creditRepository = $creditRepository;
    }

    /**
     * Validate referral code
     */
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referral_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            // Find referrer by code
            $referrer = User::where('referal_code', $request->referral_code)->first();
            
            if (!$referrer) {
                return ResponseHelper::success([
                    'valid' => false,
                    'message' => 'Invalid referral code',
                ], 'Referral code not found');
            }

            // Check if user is trying to use their own code
            if ($request->user() && $referrer->uid === $request->user()->uid) {
                return ResponseHelper::success([
                    'valid' => false,
                    'message' => 'You cannot use your own referral code',
                ], 'Invalid referral code');
            }

            return ResponseHelper::success([
                'valid' => true,
                'referrer_email' => $referrer->email,
                'referrer_name' => $referrer->name,
                'referrer_reward' => $this->referrerReward,
                'referred_reward' => $this->referredReward,
            ], 'Referral code is valid');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to validate referral code: ' . $e->getMessage());
        }
    }

    /**
     * Get my referral code
     */
    public function myCode(Request $request)
    {
        try {
            $user = $request->user();

            return ResponseHelper::success([
                'referral_code' => $user->referal_code,
                'referrer_reward' => $this->referrerReward,
                'referred_reward' => $this->referredReward,
            ], 'Referral code retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get referral code: ' . $e->getMessage());
        }
    }

    /**
     * Apply referral code (for new users during registration)
     * This should be called from AuthService during registration
     */
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referral_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $referredUser = $request->user();
            
            // Check if user already used a referral code
            $existingReward = ReferralReward::where('referred_user_uid', $referredUser->uid)->first();
            if ($existingReward) {
                return ResponseHelper::error('You have already used a referral code', 400);
            }

            // Find referrer by code
            $referrer = User::where('referal_code', $request->referral_code)->first();
            
            if (!$referrer) {
                return ResponseHelper::error('Invalid referral code', 404);
            }

            if ($referrer->uid === $referredUser->uid) {
                return ResponseHelper::error('You cannot use your own referral code', 400);
            }

            // Create referral reward record
            $reward = ReferralReward::create([
                'referrer_user_uid' => $referrer->uid,
                'referred_user_uid' => $referredUser->uid,
                'referrer_credits_earned' => $this->referrerReward,
                'referred_credits_earned' => $this->referredReward,
            ]);

            // Add credits to both users
            $this->creditRepository->addCredits($referrer->uid, $this->referrerReward);
            $this->creditRepository->addCredits($referredUser->uid, $this->referredReward);

            return ResponseHelper::success([
                'reward' => $reward,
                'credits_earned' => $this->referredReward,
            ], 'Referral code applied successfully! You earned ' . $this->referredReward . ' credits');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to apply referral code: ' . $e->getMessage());
        }
    }

    /**
     * Get my referral statistics
     */
    public function statistics(Request $request)
    {
        try {
            $user = $request->user();
            $userUid = $user->uid;

            $referrals = ReferralReward::where('referrer_user_uid', $userUid)
                ->with('referred:uid,name,email,created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            $stats = [
                'my_referral_code' => $user->referal_code,
                'total_referrals' => $referrals->count(),
                'total_bonus_earned' => $referrals->sum('referrer_credits_earned'),
                'referrals' => $referrals->map(function ($reward) {
                    return [
                        'email' => $reward->referred->email,
                        'name' => $reward->referred->name,
                        'date' => $reward->created_at->format('Y-m-d'),
                        'credits_earned' => $reward->referrer_credits_earned,
                    ];
                }),
            ];

            return ResponseHelper::success($stats, 'Referral statistics retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get referral history (who I referred)
     */
    public function history(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $perPage = $request->input('per_page', 15);

            $referrals = ReferralReward::where('referrer_user_uid', $userUid)
                ->with('referred:uid,name,email,created_at')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return ResponseHelper::success($referrals, 'Referral history retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get referral history: ' . $e->getMessage());
        }
    }

    /**
     * Check if I was referred by someone
     */
    public function myReferrer(Request $request)
    {
        try {
            $userUid = $request->user()->uid;

            $reward = ReferralReward::where('referred_user_uid', $userUid)
                ->with('referrer:uid,name,email')
                ->first();

            if (!$reward) {
                return ResponseHelper::success([
                    'has_referrer' => false,
                    'message' => 'You were not referred by anyone',
                ], 'No referrer found');
            }

            return ResponseHelper::success([
                'has_referrer' => true,
                'referrer' => [
                    'name' => $reward->referrer->name,
                    'email' => $reward->referrer->email,
                ],
                'credits_earned' => $reward->referred_credits_earned,
                'date' => $reward->created_at->format('Y-m-d'),
            ], 'Referrer information retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get referrer information: ' . $e->getMessage());
        }
    }
}
