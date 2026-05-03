<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Mobile\PartnershipService;
use Illuminate\Http\Request;

/**
 * Partnership Controller for Mobile App
 * Handles JLPT classes and internship partnerships
 */
class PartnershipController extends Controller
{
    protected $partnershipService;

    public function __construct(PartnershipService $partnershipService)
    {
        $this->partnershipService = $partnershipService;
    }

    /**
     * Get all JLPT classes
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJlptClasses(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            
            $result = $this->partnershipService->getJlptClasses($perPage, $page);

            if (!$result['success']) {
                return ResponseHelper::error($result['message']);
            }

            return ResponseHelper::success($result['data'], $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get JLPT classes: ' . $e->getMessage());
        }
    }

    /**
     * Get JLPT class details
     * 
     * @param string $uid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJlptClassDetails(string $uid)
    {
        try {
            $result = $this->partnershipService->getJlptClassDetails($uid);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 404);
            }

            return ResponseHelper::success($result['data'], $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get JLPT class details: ' . $e->getMessage());
        }
    }

    /**
     * Get all internships
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInternships(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            
            $result = $this->partnershipService->getInternships($perPage, $page);

            if (!$result['success']) {
                return ResponseHelper::error($result['message']);
            }

            return ResponseHelper::success($result['data'], $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get internships: ' . $e->getMessage());
        }
    }

    /**
     * Get internship details
     * 
     * @param string $uid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInternshipDetails(string $uid)
    {
        try {
            $result = $this->partnershipService->getInternshipDetails($uid);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 404);
            }

            return ResponseHelper::success($result['data'], $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get internship details: ' . $e->getMessage());
        }
    }

    /**
     * Submit JLPT class inquiry
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitJlptInquiry(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $result = $this->partnershipService->submitJlptInquiry($userUid, $request->all());

            if (!$result['success']) {
                if (isset($result['errors'])) {
                    return ResponseHelper::validationError($result['errors']);
                }
                return ResponseHelper::error($result['message'], 400);
            }

            $responseData = [
                'inquiry' => $result['data']
            ];

            return ResponseHelper::success($responseData, $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to submit JLPT inquiry: ' . $e->getMessage());
        }
    }

    /**
     * Submit internship inquiry
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitInternshipInquiry(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $result = $this->partnershipService->submitInternshipInquiry($userUid, $request->all());

            if (!$result['success']) {
                if (isset($result['errors'])) {
                    return ResponseHelper::validationError($result['errors']);
                }
                return ResponseHelper::error($result['message'], 400);
            }

            $responseData = [
                'inquiry' => $result['data']
            ];

            return ResponseHelper::success($responseData, $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to submit internship inquiry: ' . $e->getMessage());
        }
    }

    /**
     * Get user's inquiries
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyInquiries(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $result = $this->partnershipService->getUserInquiries($userUid);

            if (!$result['success']) {
                return ResponseHelper::error($result['message']);
            }

            return ResponseHelper::success($result['data'], $result['message']);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get inquiries: ' . $e->getMessage());
        }
    }
}