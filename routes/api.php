<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Struktur API:
| - /api/mobile/* : Untuk Mobile App (User)
| - /api/cms/*    : Untuk CMS (Admin)
|
*/

// ============================================================================
// MOBILE APP ROUTES
// ============================================================================

Route::prefix('mobile')->group(function () {
    
    // Authentication Routes (Public)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [\App\Http\Controllers\Mobile\AuthController::class, 'register']);
        Route::post('/login', [\App\Http\Controllers\Mobile\AuthController::class, 'login']);
        Route::get('/verify-email/{token}', [\App\Http\Controllers\Mobile\AuthController::class, 'verifyEmail']);
        Route::post('/verify-email-otp', [\App\Http\Controllers\Mobile\AuthController::class, 'verifyEmailWithOTP']);
        Route::post('/resend-verification', [\App\Http\Controllers\Mobile\AuthController::class, 'resendVerification']);
        Route::post('/forgot-password', [\App\Http\Controllers\Mobile\AuthController::class, 'forgotPassword']);
        Route::post('/verify-reset-otp', [\App\Http\Controllers\Mobile\AuthController::class, 'verifyResetOTP']);
        Route::post('/reset-password', [\App\Http\Controllers\Mobile\AuthController::class, 'resetPassword']);
    });

    // Protected Routes (Require Authentication)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Authentication
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [\App\Http\Controllers\Mobile\AuthController::class, 'logout']);
            Route::post('/refresh-token', [\App\Http\Controllers\Mobile\AuthController::class, 'refreshToken']);
            Route::get('/profile', [\App\Http\Controllers\Mobile\AuthController::class, 'profile']);
            Route::put('/profile', [\App\Http\Controllers\Mobile\AuthController::class, 'updateProfile']);
            Route::post('/change-password', [\App\Http\Controllers\Mobile\AuthController::class, 'changePassword']);
        });

        // User Credits
        Route::prefix('credits')->group(function () {
            Route::get('/', [\App\Http\Controllers\Mobile\UserCreditController::class, 'myCredit']);
            Route::get('/balance', [\App\Http\Controllers\Mobile\UserCreditController::class, 'balance']);
            Route::get('/streak', [\App\Http\Controllers\Mobile\UserCreditController::class, 'streak']);
            Route::get('/cycle', [\App\Http\Controllers\Mobile\UserCreditController::class, 'cycleInfo']);
        });

        // Daily Login
        Route::prefix('daily-login')->group(function () {
            Route::get('/status', [\App\Http\Controllers\Mobile\DailyLoginController::class, 'status']);
            Route::post('/claim', [\App\Http\Controllers\Mobile\DailyLoginController::class, 'claim']);
            Route::get('/history', [\App\Http\Controllers\Mobile\DailyLoginController::class, 'history']);
            Route::get('/can-claim', [\App\Http\Controllers\Mobile\DailyLoginController::class, 'canClaim']);
        });

        // User Progress
        Route::prefix('progress')->group(function () {
            Route::get('/', [\App\Http\Controllers\Mobile\UserProgressController::class, 'index']);
            Route::get('/summary', [\App\Http\Controllers\Mobile\UserProgressController::class, 'summary']);
            Route::put('/update', [\App\Http\Controllers\Mobile\UserProgressController::class, 'updateScores']);
            Route::post('/lesson-complete', [\App\Http\Controllers\Mobile\UserProgressController::class, 'completeLesson']);
        });

        // JLPT Lessons & Tests
        Route::prefix('jlpt')->group(function () {
            Route::get('/lessons/{level}', [\App\Http\Controllers\Mobile\JlptController::class, 'getLessons']);
            Route::post('/lessons/complete', [\App\Http\Controllers\Mobile\JlptController::class, 'completeLesson']);
            Route::post('/test/submit', [\App\Http\Controllers\Mobile\JlptController::class, 'submitTest']);
            Route::get('/test/history', [\App\Http\Controllers\Mobile\JlptController::class, 'getTestHistory']);
            Route::get('/test/best-scores', [\App\Http\Controllers\Mobile\JlptController::class, 'getBestScores']);
        });

        // User Notes
        Route::prefix('notes')->group(function () {
            Route::get('/', [\App\Http\Controllers\Mobile\UserNoteController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Mobile\UserNoteController::class, 'store']);
            Route::get('/{uid}', [\App\Http\Controllers\Mobile\UserNoteController::class, 'show']);
            Route::put('/{uid}', [\App\Http\Controllers\Mobile\UserNoteController::class, 'update']);
            Route::delete('/{uid}', [\App\Http\Controllers\Mobile\UserNoteController::class, 'destroy']);
        });

        // Kanji
        Route::prefix('kanji')->group(function () {
            Route::get('/', [\App\Http\Controllers\Mobile\KanjiController::class, 'index']);
            Route::get('/{uid}', [\App\Http\Controllers\Mobile\KanjiController::class, 'show']);
            Route::get('/favorites/list', [\App\Http\Controllers\Mobile\KanjiController::class, 'favorites']);
            Route::post('/{kanjiUid}/favorite', [\App\Http\Controllers\Mobile\KanjiController::class, 'addFavorite']);
            Route::delete('/{kanjiUid}/favorite', [\App\Http\Controllers\Mobile\KanjiController::class, 'removeFavorite']);
        });

        // Vocabulary
        Route::prefix('vocabulary')->group(function () {
            Route::get('/categories', [\App\Http\Controllers\Mobile\VocabularyController::class, 'categories']);
            Route::get('/category/{categoryUid}', [\App\Http\Controllers\Mobile\VocabularyController::class, 'wordsByCategory']);
            Route::get('/{uid}', [\App\Http\Controllers\Mobile\VocabularyController::class, 'show']);
            Route::get('/favorites/list', [\App\Http\Controllers\Mobile\VocabularyController::class, 'favorites']);
            Route::post('/{wordUid}/favorite', [\App\Http\Controllers\Mobile\VocabularyController::class, 'addFavorite']);
            Route::delete('/{wordUid}/favorite', [\App\Http\Controllers\Mobile\VocabularyController::class, 'removeFavorite']);
        });

        // Leaderboard
        Route::prefix('leaderboard')->group(function () {
            Route::get('/', [\App\Http\Controllers\Mobile\LeaderboardController::class, 'index']);
            Route::get('/my-rank', [\App\Http\Controllers\Mobile\LeaderboardController::class, 'myRank']);
        });

        // Certificates
        Route::prefix('certificates')->group(function () {
            Route::get('/', [\App\Http\Controllers\Mobile\CertificateController::class, 'index']);
            Route::get('/check/{level}', [\App\Http\Controllers\Mobile\CertificateController::class, 'checkEligibility']);
            Route::post('/generate', [\App\Http\Controllers\Mobile\CertificateController::class, 'generate']);
            Route::get('/{uid}/download', [\App\Http\Controllers\Mobile\CertificateController::class, 'download']);
        });

        // Ad Watches
        Route::prefix('ads')->group(function () {
            Route::get('/status', [\App\Http\Controllers\Mobile\AdWatchController::class, 'status']);
            Route::post('/watch', [\App\Http\Controllers\Mobile\AdWatchController::class, 'watch']);
            Route::get('/can-watch/{adType}', [\App\Http\Controllers\Mobile\AdWatchController::class, 'canWatch']);
            Route::get('/history', [\App\Http\Controllers\Mobile\AdWatchController::class, 'history']);
        });

        // Conversations
        Route::prefix('conversations')->group(function () {
            Route::get('/', [\App\Http\Controllers\Mobile\ConversationController::class, 'index']);
            Route::get('/{uid}', [\App\Http\Controllers\Mobile\ConversationController::class, 'show']);
        });

        // Quiz
        Route::prefix('quiz')->group(function () {
            Route::post('/submit', [\App\Http\Controllers\Mobile\QuizController::class, 'submit']);
            Route::get('/history', [\App\Http\Controllers\Mobile\QuizController::class, 'history']);
            Route::get('/statistics', [\App\Http\Controllers\Mobile\QuizController::class, 'statistics']);
        });

        // Referral
        Route::prefix('referral')->group(function () {
            Route::post('/validate', [\App\Http\Controllers\Mobile\ReferralController::class, 'validate']);
            Route::get('/my-code', [\App\Http\Controllers\Mobile\ReferralController::class, 'myCode']);
            Route::post('/apply', [\App\Http\Controllers\Mobile\ReferralController::class, 'apply']);
            Route::get('/statistics', [\App\Http\Controllers\Mobile\ReferralController::class, 'statistics']);
            Route::get('/history', [\App\Http\Controllers\Mobile\ReferralController::class, 'history']);
            Route::get('/my-referrer', [\App\Http\Controllers\Mobile\ReferralController::class, 'myReferrer']);
        });

        // Partnership
        Route::prefix('partnership')->group(function () {
            // JLPT Classes
            Route::prefix('jlpt-classes')->group(function () {
                Route::get('/', [\App\Http\Controllers\Mobile\PartnershipController::class, 'getJlptClasses']);
                Route::get('/{uid}', [\App\Http\Controllers\Mobile\PartnershipController::class, 'getJlptClassDetails']);
                Route::post('/inquire', [\App\Http\Controllers\Mobile\PartnershipController::class, 'submitJlptInquiry']);
            });

            // Internships
            Route::prefix('internships')->group(function () {
                Route::get('/', [\App\Http\Controllers\Mobile\PartnershipController::class, 'getInternships']);
                Route::get('/{uid}', [\App\Http\Controllers\Mobile\PartnershipController::class, 'getInternshipDetails']);
                Route::post('/inquire', [\App\Http\Controllers\Mobile\PartnershipController::class, 'submitInternshipInquiry']);
            });

            // User Inquiries
            Route::get('/my-inquiries', [\App\Http\Controllers\Mobile\PartnershipController::class, 'getMyInquiries']);
        });
    });
});

// ============================================================================
// CMS (ADMIN) ROUTES
// ============================================================================

Route::prefix('cms')->group(function () {
    
    // Admin Authentication (Public)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [\App\Http\Controllers\CMS\AuthController::class, 'login']);
    });

    // Protected Admin Routes (Require Authentication)
    // TODO: Add admin middleware to verify admin role
    Route::middleware('auth:sanctum')->group(function () {
        
        // Admin Authentication
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [\App\Http\Controllers\CMS\AuthController::class, 'logout']);
            Route::get('/profile', [\App\Http\Controllers\CMS\AuthController::class, 'profile']);
        });

        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [\App\Http\Controllers\CMS\AuthController::class, 'getAllUsers']);
            Route::get('/{userUid}', [\App\Http\Controllers\CMS\AuthController::class, 'getUserDetails']);
            Route::post('/{userUid}/block', [\App\Http\Controllers\CMS\AuthController::class, 'blockUser']);
            Route::post('/{userUid}/unblock', [\App\Http\Controllers\CMS\AuthController::class, 'unblockUser']);
        });

        // User Credits Management
        Route::prefix('credits')->group(function () {
            Route::get('/', [\App\Http\Controllers\CMS\UserCreditController::class, 'index']);
            Route::get('/top-users', [\App\Http\Controllers\CMS\UserCreditController::class, 'topUsers']);
            Route::get('/statistics', [\App\Http\Controllers\CMS\UserCreditController::class, 'statistics']);
            
            Route::prefix('user/{userUid}')->group(function () {
                Route::get('/', [\App\Http\Controllers\CMS\UserCreditController::class, 'show']);
                Route::post('/add', [\App\Http\Controllers\CMS\UserCreditController::class, 'addCredits']);
                Route::post('/deduct', [\App\Http\Controllers\CMS\UserCreditController::class, 'deductCredits']);
                Route::post('/add-points', [\App\Http\Controllers\CMS\UserCreditController::class, 'addPoints']);
                Route::post('/update-streak', [\App\Http\Controllers\CMS\UserCreditController::class, 'updateStreak']);
                Route::post('/reset-cycle', [\App\Http\Controllers\CMS\UserCreditController::class, 'resetCycle']);
            });
        });

        // Daily Login Management
        Route::prefix('daily-login')->group(function () {
            Route::get('/user/{userUid}/status', [\App\Http\Controllers\CMS\DailyLoginController::class, 'getUserStatus']);
            Route::get('/user/{userUid}/history', [\App\Http\Controllers\CMS\DailyLoginController::class, 'getUserHistory']);
            Route::post('/user/{userUid}/manual-claim', [\App\Http\Controllers\CMS\DailyLoginController::class, 'manualClaim']);
        });

        // TODO: Add more CMS routes here
        // - User Progress Management
        // - JLPT Content Management
        // - Kanji Management
        // - Vocabulary Management
        // - Certificate Management
        // - Ad Management
        // - Leaderboard Management
    });
});

// ============================================================================
// LEGACY ROUTES (For backward compatibility - will be deprecated)
// ============================================================================

// Route::prefix('auth')->group(function () {
//     Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
//     Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
//     Route::get('/verify-email/{token}', [\App\Http\Controllers\AuthController::class, 'verifyEmail']);
//     Route::post('/resend-verification', [\App\Http\Controllers\AuthController::class, 'resendVerificationEmail']);
// });

// Route::middleware('auth:sanctum')->group(function () {
//     Route::prefix('auth')->group(function () {
//         Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
//         Route::post('/refresh-token', [\App\Http\Controllers\AuthController::class, 'refreshToken']);
//         Route::get('/profile', [\App\Http\Controllers\AuthController::class, 'profile']);
//         Route::put('/profile', [\App\Http\Controllers\AuthController::class, 'updateProfile']);
//         Route::post('/change-password', [\App\Http\Controllers\AuthController::class, 'changePassword']);
//         Route::post('/block-user/{uid}', [\App\Http\Controllers\AuthController::class, 'blockUser']);
//         Route::post('/unblock-user/{uid}', [\App\Http\Controllers\AuthController::class, 'unblockUser']);
//     });

//     Route::prefix('my-credits')->group(function () {
//         Route::get('/', [\App\Http\Controllers\UserCreditController::class, 'myCredit']);
//         Route::post('/add', [\App\Http\Controllers\UserCreditController::class, 'addMyCredits']);
//         Route::post('/deduct', [\App\Http\Controllers\UserCreditController::class, 'deductMyCredits']);
//         Route::post('/update-streak', [\App\Http\Controllers\UserCreditController::class, 'updateMyStreak']);
//         Route::post('/reset-cycle', [\App\Http\Controllers\UserCreditController::class, 'resetMyCycle']);
//     });

//     Route::prefix('admin/credits')->group(function () {
//         Route::get('/user/{userUid}', [\App\Http\Controllers\UserCreditController::class, 'showByUserUid']);
//         Route::post('/user/{userUid}/add', [\App\Http\Controllers\UserCreditController::class, 'addCredits']);
//         Route::post('/user/{userUid}/deduct', [\App\Http\Controllers\UserCreditController::class, 'deductCredits']);
//         Route::post('/user/{userUid}/update-streak', [\App\Http\Controllers\UserCreditController::class, 'updateStreak']);
//         Route::post('/user/{userUid}/reset-cycle', [\App\Http\Controllers\UserCreditController::class, 'resetCycle']);
//     });
// });
