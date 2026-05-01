<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;

/**
 * Conversation Controller for Mobile App
 */
class ConversationController extends Controller
{
    /**
     * Get all conversations
     */
    public function index(Request $request)
    {
        try {
            $type = $request->input('type');
            $difficulty = $request->input('difficulty');

            $query = Conversation::query();

            if ($type) {
                $query->where('type', $type);
            }

            if ($difficulty) {
                $query->where('difficulty', $difficulty);
            }

            $conversations = $query->orderBy('display_order', 'asc')->get();

            return ResponseHelper::success($conversations, 'Conversations retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get conversations: ' . $e->getMessage());
        }
    }

    /**
     * Get conversation with dialogs
     */
    public function show($uid)
    {
        try {
            $conversation = Conversation::with('dialogs')->where('uid', $uid)->first();

            if (!$conversation) {
                return ResponseHelper::notFound('Conversation not found');
            }

            return ResponseHelper::success($conversation, 'Conversation retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get conversation: ' . $e->getMessage());
        }
    }
}
