<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Mobile\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Certificate Controller for Mobile App
 */
class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Get my certificates
     */
    public function index(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $certificates = $this->certificateService->getUserCertificates($userUid);

            return ResponseHelper::success($certificates, 'Certificates retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get certificates: ' . $e->getMessage());
        }
    }

    /**
     * Check if can generate certificate
     */
    public function checkEligibility(Request $request, $level)
    {
        try {
            $userUid = $request->user()->uid;
            $eligibility = $this->certificateService->canGenerateCertificate($userUid, strtoupper($level));

            return ResponseHelper::success($eligibility, 'Eligibility checked successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to check eligibility: ' . $e->getMessage());
        }
    }

    /**
     * Generate certificate
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|in:N5,N4,N3,N2,N1',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $userUid = $request->user()->uid;
            $result = $this->certificateService->generateCertificate($userUid, $request->level);

            if (!$result['success']) {
                return ResponseHelper::error($result['message'], 400);
            }

            return ResponseHelper::success($result, $result['message'], 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to generate certificate: ' . $e->getMessage());
        }
    }

    /**
     * Download certificate (placeholder)
     */
    public function download($uid)
    {
        try {
            // TODO: Implement actual PDF generation
            return ResponseHelper::success([
                'download_url' => url("/certificates/{$uid}/download"),
                'message' => 'Certificate download URL generated',
            ], 'Download URL retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to download certificate: ' . $e->getMessage());
        }
    }
}
