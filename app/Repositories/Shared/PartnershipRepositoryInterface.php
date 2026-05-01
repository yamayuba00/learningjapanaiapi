<?php

namespace App\Repositories\Shared;

interface PartnershipRepositoryInterface
{
    /**
     * Get all active JLPT classes
     */
    public function getActiveJlptClasses();

    /**
     * Get JLPT class by UID
     */
    public function getJlptClassByUid(string $uid);

    /**
     * Get all active internships
     */
    public function getActiveInternships();

    /**
     * Get internship by UID
     */
    public function getInternshipByUid(string $uid);

    /**
     * Submit JLPT class inquiry
     */
    public function submitJlptInquiry(array $data);

    /**
     * Submit internship inquiry
     */
    public function submitInternshipInquiry(array $data);

    /**
     * Get user's partnership inquiries
     */
    public function getUserInquiries(string $userUid);
}