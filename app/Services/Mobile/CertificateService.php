<?php

namespace App\Services\Mobile;

use App\Repositories\Shared\CertificateRepositoryInterface;
use App\Repositories\Shared\JlptRepositoryInterface;
use App\Repositories\Shared\UserCreditRepositoryInterface;
use Carbon\Carbon;

class CertificateService
{
    protected $certificateRepository;
    protected $jlptRepository;
    protected $creditRepository;

    // Certificate cost in credits
    protected $certificateCost = 60;

    // Minimum passing score
    protected $passingScore = 60;

    public function __construct(
        CertificateRepositoryInterface $certificateRepository,
        JlptRepositoryInterface $jlptRepository,
        UserCreditRepositoryInterface $creditRepository
    ) {
        $this->certificateRepository = $certificateRepository;
        $this->jlptRepository = $jlptRepository;
        $this->creditRepository = $creditRepository;
    }

    /**
     * Check if user can generate certificate
     */
    public function canGenerateCertificate(string $userUid, string $level): array
    {
        // Check if already has certificate
        $exists = $this->certificateRepository->checkIfExists($userUid, $level);
        if ($exists) {
            return [
                'can_generate' => false,
                'reason' => 'Certificate already generated for this level',
            ];
        }

        // Check if user has passed exam
        $examScore = $this->jlptRepository->getBestScore($userUid, $level, 'exam');
        
        if (!$examScore) {
            return [
                'can_generate' => false,
                'reason' => 'No exam score found for this level',
            ];
        }

        if ($examScore->score < $this->passingScore) {
            return [
                'can_generate' => false,
                'reason' => 'Exam score is below passing score (60%)',
                'your_score' => $examScore->score,
            ];
        }

        // Check if user has enough credits
        $userCredit = $this->creditRepository->findByUserUid($userUid);
        if (!$userCredit || $userCredit->credits < $this->certificateCost) {
            return [
                'can_generate' => false,
                'reason' => 'Insufficient credits',
                'required' => $this->certificateCost,
                'available' => $userCredit ? $userCredit->credits : 0,
            ];
        }

        return [
            'can_generate' => true,
            'score' => $examScore->score,
            'cost' => $this->certificateCost,
        ];
    }

    /**
     * Generate certificate
     */
    public function generateCertificate(string $userUid, string $level): array
    {
        $canGenerate = $this->canGenerateCertificate($userUid, $level);

        if (!$canGenerate['can_generate']) {
            return [
                'success' => false,
                'message' => $canGenerate['reason'],
                'details' => $canGenerate,
            ];
        }

        // Deduct credits
        $deducted = $this->creditRepository->deductCredits($userUid, $this->certificateCost);
        
        if (!$deducted) {
            return [
                'success' => false,
                'message' => 'Failed to deduct credits',
            ];
        }

        // Create certificate
        $certificate = $this->certificateRepository->create([
            'user_uid' => $userUid,
            'level' => $level,
            'score' => $canGenerate['score'],
            'credits_spent' => $this->certificateCost,
            'downloaded_at' => Carbon::now(),
        ]);

        return [
            'success' => true,
            'message' => 'Certificate generated successfully',
            'certificate' => $certificate,
            'credits_spent' => $this->certificateCost,
        ];
    }

    /**
     * Get user certificates
     */
    public function getUserCertificates(string $userUid)
    {
        return $this->certificateRepository->findByUserUid($userUid);
    }
}
