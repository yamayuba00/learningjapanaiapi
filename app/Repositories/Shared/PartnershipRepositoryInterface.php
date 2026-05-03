<?php

namespace App\Repositories\Shared;

interface PartnershipRepositoryInterface
{
    /**
     * Get all active JLPT classes with pagination
     */
    public function getActiveJlptClasses(int $perPage = 10, int $page = 1);

    /**
     * Get JLPT class by UID
     */
    public function getJlptClassByUid(string $uid);

    /**
     * Get all active internships with pagination
     */
    public function getActiveInternships(int $perPage = 10, int $page = 1);

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