<?php

namespace App\Repositories\Shared;

use App\Models\PartnershipJlptClass;
use App\Models\PartnershipInternship;
use App\Models\PartnershipInquiry;

class PartnershipRepository implements PartnershipRepositoryInterface
{
    protected $jlptClassModel;
    protected $internshipModel;
    protected $inquiryModel;

    public function __construct(
        PartnershipJlptClass $jlptClassModel,
        PartnershipInternship $internshipModel,
        PartnershipInquiry $inquiryModel
    ) {
        $this->jlptClassModel = $jlptClassModel;
        $this->internshipModel = $internshipModel;
        $this->inquiryModel = $inquiryModel;
    }

    /**
     * Get all active JLPT classes
     */
    public function getActiveJlptClasses()
    {
        return $this->jlptClassModel
            ->where('is_active', true)
            ->where('is_verified', true)
            ->orderBy('display_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get JLPT class by UID
     */
    public function getJlptClassByUid(string $uid)
    {
        return $this->jlptClassModel
            ->where('uid', $uid)
            ->where('is_active', true)
            ->where('is_verified', true)
            ->first();
    }

    /**
     * Get all active internships
     */
    public function getActiveInternships()
    {
        return $this->internshipModel
            ->where('is_active', true)
            ->where('is_verified', true)
            ->orderBy('display_order', 'asc')
            ->orderBy('success_rate', 'desc')
            ->get();
    }

    /**
     * Get internship by UID
     */
    public function getInternshipByUid(string $uid)
    {
        return $this->internshipModel
            ->where('uid', $uid)
            ->where('is_active', true)
            ->where('is_verified', true)
            ->first();
    }

    /**
     * Submit JLPT class inquiry
     */
    public function submitJlptInquiry(array $data)
    {
        return $this->inquiryModel->create([
            'user_uid' => $data['user_uid'],
            'type' => 'jlpt_class',
            'partner_uid' => $data['partner_uid'],
            'partner_name' => $data['partner_name'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'message' => $data['message'] ?? null,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Submit internship inquiry
     */
    public function submitInternshipInquiry(array $data)
    {
        return $this->inquiryModel->create([
            'user_uid' => $data['user_uid'],
            'type' => 'internship',
            'partner_uid' => $data['partner_uid'],
            'partner_name' => $data['partner_name'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'message' => $data['message'] ?? null,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Get user's partnership inquiries
     */
    public function getUserInquiries(string $userUid)
    {
        return $this->inquiryModel
            ->where('user_uid', $userUid)
            ->orderBy('submitted_at', 'desc')
            ->get();
    }
}