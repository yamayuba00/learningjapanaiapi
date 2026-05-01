<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Kanji;
use App\Models\KanjiFavorite;
use Illuminate\Http\Request;

/**
 * Kanji Controller for Mobile App
 */
class KanjiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 20);
            $level = $request->input('level');

            $query = Kanji::query();

            if ($level) {
                $query->where('level', $level);
            }

            $kanji = $query->orderBy('stroke_count', 'asc')->paginate($perPage);

            return ResponseHelper::success($kanji, 'Kanji retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get kanji: ' . $e->getMessage());
        }
    }

    public function show($uid)
    {
        try {
            $kanji = Kanji::with('examples')->where('uid', $uid)->first();

            if (!$kanji) {
                return ResponseHelper::notFound('Kanji not found');
            }

            return ResponseHelper::success($kanji, 'Kanji details retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get kanji: ' . $e->getMessage());
        }
    }

    public function favorites(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $favorites = KanjiFavorite::with('kanji')
                ->where('user_uid', $userUid)
                ->orderBy('created_at', 'desc')
                ->get();

            return ResponseHelper::success($favorites, 'Favorite kanji retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get favorites: ' . $e->getMessage());
        }
    }

    public function addFavorite(Request $request, $kanjiUid)
    {
        try {
            $userUid = $request->user()->uid;

            $exists = KanjiFavorite::where('user_uid', $userUid)
                ->where('kanji_uid', $kanjiUid)
                ->exists();

            if ($exists) {
                return ResponseHelper::error('Kanji already in favorites', 400);
            }

            $favorite = KanjiFavorite::create([
                'user_uid' => $userUid,
                'kanji_uid' => $kanjiUid,
            ]);

            return ResponseHelper::success($favorite, 'Kanji added to favorites', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to add favorite: ' . $e->getMessage());
        }
    }

    public function removeFavorite(Request $request, $kanjiUid)
    {
        try {
            $userUid = $request->user()->uid;

            $deleted = KanjiFavorite::where('user_uid', $userUid)
                ->where('kanji_uid', $kanjiUid)
                ->delete();

            if (!$deleted) {
                return ResponseHelper::notFound('Favorite not found');
            }

            return ResponseHelper::success(null, 'Kanji removed from favorites');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to remove favorite: ' . $e->getMessage());
        }
    }
}
