<?php

namespace App\Http\Controllers\Mobile;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Shared\UserNoteRepositoryInterface as SharedUserNoteRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * User Note Controller for Mobile App
 * Handles Indonesian text notes (translation done on client-side)
 */
class UserNoteController extends Controller
{
    protected $noteRepository;

    public function __construct(SharedUserNoteRepositoryInterface $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function index(Request $request)
    {
        try {
            $userUid = $request->user()->uid;
            $perPage = $request->input('per_page', 15);
            $notes = $this->noteRepository->getPaginated($userUid, $perPage);

            return ResponseHelper::success($notes, 'Notes retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get notes: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'indonesian_text' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $note = $this->noteRepository->create([
                'user_uid' => $request->user()->uid,
                'indonesian_text' => $request->indonesian_text,
            ]);

            return ResponseHelper::success($note, 'Note created successfully', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to create note: ' . $e->getMessage());
        }
    }

    public function show($uid)
    {
        try {
            $note = $this->noteRepository->findByUid($uid);
            
            if (!$note) {
                return ResponseHelper::notFound('Note not found');
            }

            return ResponseHelper::success($note, 'Note retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to get note: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $uid)
    {
        $validator = Validator::make($request->all(), [
            'indonesian_text' => 'sometimes|string|max:1000',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        try {
            $note = $this->noteRepository->update($uid, $request->all());

            if (!$note) {
                return ResponseHelper::notFound('Note not found');
            }

            return ResponseHelper::success($note, 'Note updated successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to update note: ' . $e->getMessage());
        }
    }

    public function destroy($uid)
    {
        try {
            $deleted = $this->noteRepository->delete($uid);

            if (!$deleted) {
                return ResponseHelper::notFound('Note not found');
            }

            return ResponseHelper::success(null, 'Note deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to delete note: ' . $e->getMessage());
        }
    }
}
