<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\VocabularyCategory;
use App\Models\VocabularyWord;
use App\Models\VocabularyFavorite;
use Illuminate\Http\Request;

/**
 * Vocabulary Controller for Mobile App
 */
class VocabularyController extends Controller
{
    public function categories()
    {
        try {
            $categories = VocabularyCategory::orderBy('display_order', 'asc')->get();

            return ResponseHelper::success($categories, 'Categories retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get categories: ' . $e->getMessage());
        }
    }

    public function wordsByCategory(Request $request, $categoryUid)
    {
        try {
            $perPage = $request->input('per_page', 20);
            $words = VocabularyWord::where('category_uid', $categoryUid)
                ->orderBy('japanese', 'asc')
                ->paginate($perPage);

            return ResponseHelper::success($words, 'Words retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get words: ' . $e->getMessage());
        }
    }

    public function show($uid)
    {
        try {
            $word = VocabularyWord::with('category')->where('uid', $uid)->first();

            if (!$word) {
                return ResponseHelper::notFound('Word not found');
            }

            return ResponseHelper::success($word, 'Word details retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get word: ' . $e->getMessage());
        }
    }

    public function favorites(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $favorites = VocabularyFavorite::with('word.category')
                ->where('user_uid', $userUid)
                ->orderBy('created_at', 'desc')
                ->get();

            return ResponseHelper::success($favorites, 'Favorite words retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get favorites: ' . $e->getMessage());
        }
    }

    public function addFavorite(Request $request, $wordUid)
    {
        try {
            $userUid = $request->user()->uid;

            $exists = VocabularyFavorite::where('user_uid', $userUid)
                ->where('word_uid', $wordUid)
                ->exists();

            if ($exists) {
                return ResponseHelper::error('Word already in favorites', 400);
            }

            $favorite = VocabularyFavorite::create([
                'user_uid' => $userUid,
                'word_uid' => $wordUid,
            ]);

            return ResponseHelper::success($favorite, 'Word added to favorites', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to add favorite: ' . $e->getMessage());
        }
    }

    public function removeFavorite(Request $request, $wordUid)
    {
        try {
            $userUid = $request->user()->uid;

            $deleted = VocabularyFavorite::where('user_uid', $userUid)
                ->where('word_uid', $wordUid)
                ->delete();

            if (!$deleted) {
                return ResponseHelper::notFound('Favorite not found');
            }

            return ResponseHelper::success(null, 'Word removed from favorites');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to remove favorite: ' . $e->getMessage());
        }
    }
}
