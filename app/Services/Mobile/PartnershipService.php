<?php

namespace App\Services\Mobile;

use App\Repositories\Shared\PartnershipRepositoryInterface;
use Illuminate\Support\Facades\Validator;

/**
 * Partnership Service for Mobile App
 * Handles JLPT classes and internship partnerships
 */
class PartnershipService
{
    protected $partnershipRepository;

    public function __construct(PartnershipRepositoryInterface $partnershipRepository)
    {
        $this->partnershipRepository = $partnershipRepository;
    }

    /**
     * Get all JLPT classes
     */
    public function getJlptClasses(): array
    {
        try {
            $classes = $this->partnershipRepository->getActiveJlptClasses();

            return [
                'success' => true,
                'data' => $classes,
                'message' => 'JLPT classes retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get JLPT classes: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get JLPT class details
     */
    public function getJlptClassDetails(string $uid): array
    {
        try {
            $class = $this->partnershipRepository->getJlptClassByUid($uid);

            if (!$class) {
                return [
                    'success' => false,
                    'message' => 'JLPT class not found'
                ];
            }

            return [
                'success' => true,
                'data' => $class,
                'message' => 'JLPT class details retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get JLPT class details: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all internships
     */
    public function getInternships(): array
    {
        try {
            $internships = $this->partnershipRepository->getActiveInternships();

            return [
                'success' => true,
                'data' => $internships,
                'message' => 'Internships retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get internships: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get internship details
     */
    public function getInternshipDetails(string $uid): array
    {
        try {
            $internship = $this->partnershipRepository->getInternshipByUid($uid);

            if (!$internship) {
                return [
                    'success' => false,
                    'message' => 'Internship not found'
                ];
            }

            return [
                'success' => true,
                'data' => $internship,
                'message' => 'Internship details retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get internship details: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Submit JLPT class inquiry
     */
    public function submitJlptInquiry(string $userUid, array $data): array
    {
        try {
            // Validate input
            $validator = Validator::make($data, [
                'partner_uid' => 'required|string',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'message' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ];
            }

            // Get JLPT class details
            $jlptClass = $this->partnershipRepository->getJlptClassByUid($data['partner_uid']);
            if (!$jlptClass) {
                return [
                    'success' => false,
                    'message' => 'JLPT class not found'
                ];
            }

            // Submit inquiry
            $inquiry = $this->partnershipRepository->submitJlptInquiry([
                'user_uid' => $userUid,
                'partner_uid' => $data['partner_uid'],
                'partner_name' => $jlptClass->name,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'message' => $data['message'] ?? null,
            ]);

            return [
                'success' => true,
                'data' => $inquiry,
                'message' => 'JLPT class inquiry submitted successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to submit inquiry: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Submit internship inquiry
     */
    public function submitInternshipInquiry(string $userUid, array $data): array
    {
        try {
            // Validate input
            $validator = Validator::make($data, [
                'partner_uid' => 'required|string',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'message' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ];
            }

            // Get internship details
            $internship = $this->partnershipRepository->getInternshipByUid($data['partner_uid']);
            if (!$internship) {
                return [
                    'success' => false,
                    'message' => 'Internship not found'
                ];
            }

            // Submit inquiry
            $inquiry = $this->partnershipRepository->submitInternshipInquiry([
                'user_uid' => $userUid,
                'partner_uid' => $data['partner_uid'],
                'partner_name' => $internship->name,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'message' => $data['message'] ?? null,
            ]);

            return [
                'success' => true,
                'data' => $inquiry,
                'message' => 'Internship inquiry submitted successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to submit inquiry: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user's inquiries
     */
    public function getUserInquiries(string $userUid): array
    {
        try {
            $inquiries = $this->partnershipRepository->getUserInquiries($userUid);

            return [
                'success' => true,
                'data' => $inquiries,
                'message' => 'User inquiries retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get user inquiries: ' . $e->getMessage()
            ];
        }
    }
}