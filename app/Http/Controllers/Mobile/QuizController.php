<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\QuizHistory;
use App\Repositories\UserCreditRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Quiz Controller for Mobile App
 */
class QuizController extends Controller
{
    protected $creditRepository;

    public function __construct(UserCreditRepositoryInterface $creditRepository)
    {
        $this->creditRepository = $creditRepository;
    }

    /**
     * Submit quiz result
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quiz_type' => 'required|in:hiragana,katakana,kanji,vocabulary',
            'category_id' => 'nullable|integer',
            'total_questions' => 'required|integer|min:1',
            'correct_answers' => 'required|integer|min:0',
            'wrong_answers' => 'required|integer|min:0',
            'time_spent_seconds' => 'required|integer|min:0',
            'lives_lost' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $userUid = $request->user()->uid;
            
            // Calculate score
            $score = ($request->correct_answers / $request->total_questions) * 100;
            
            // Calculate points earned (1 point per correct answer)
            $pointsEarned = $request->correct_answers;

            // Save quiz history
            $quizHistory = QuizHistory::create([
                'user_uid' => $userUid,
                'quiz_type' => $request->quiz_type,
                'category_id' => $request->category_id,
                'total_questions' => $request->total_questions,
                'correct_answers' => $request->correct_answers,
                'wrong_answers' => $request->wrong_answers,
                'score' => round($score, 2),
                'time_spent_seconds' => $request->time_spent_seconds,
                'points_earned' => $pointsEarned,
                'lives_lost' => $request->lives_lost ?? 0,
                'taken_at' => Carbon::now(),
            ]);

            // Add points to user
            $this->creditRepository->addPoints($userUid, $pointsEarned);

            return ResponseHelper::success([
                'quiz_history' => $quizHistory,
                'score' => round($score, 2),
                'points_earned' => $pointsEarned,
                'passed' => $score >= 60,
            ], 'Quiz submitted successfully', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to submit quiz: ' . $e->getMessage());
        }
    }

    /**
     * Get quiz history
     */
    public function history(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $quizType = $request->input('quiz_type');
            $perPage = $request->input('per_page', 15);

            $query = QuizHistory::where('user_uid', $userUid);

            if ($quizType) {
                $query->where('quiz_type', $quizType);
            }

            $history = $query->orderBy('taken_at', 'desc')->paginate($perPage);

            return ResponseHelper::success($history, 'Quiz history retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get quiz history: ' . $e->getMessage());
        }
    }

    /**
     * Get quiz statistics
     */
    public function statistics(Request $request)
    {
        try {
            $userUid = $request->user()->uid;

            $stats = [
                'total_quizzes' => QuizHistory::where('user_uid', $userUid)->count(),
                'total_points_earned' => QuizHistory::where('user_uid', $userUid)->sum('points_earned'),
                'average_score' => QuizHistory::where('user_uid', $userUid)->avg('score'),
                'by_type' => [],
            ];

            $types = ['hiragana', 'katakana', 'kanji', 'vocabulary'];
            foreach ($types as $type) {
                $typeQuizzes = QuizHistory::where('user_uid', $userUid)
                    ->where('quiz_type', $type)
                    ->get();

                $stats['by_type'][$type] = [
                    'total' => $typeQuizzes->count(),
                    'average_score' => $typeQuizzes->avg('score'),
                    'best_score' => $typeQuizzes->max('score'),
                ];
            }

            return ResponseHelper::success($stats, 'Quiz statistics retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get statistics: ' . $e->getMessage());
        }
    }
}
