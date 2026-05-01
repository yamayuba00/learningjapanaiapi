<?php

namespace App\Repositories\Shared;

use App\Models\UserNote;

class UserNoteRepository implements UserNoteRepositoryInterface
{
    protected $model;

    public function __construct(UserNote $model)
    {
        $this->model = $model;
    }

    public function findByUserUid(string $userUid)
    {
        return $this->model->where('user_uid', $userUid)->orderBy('created_at', 'desc')->get();
    }

    public function findByUid(string $uid)
    {
        return $this->model->where('uid', $uid)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $uid, array $data)
    {
        $note = $this->findByUid($uid);
        if ($note) {
            $note->update($data);
            return $note;
        }
        return null;
    }

    public function delete(string $uid): bool
    {
        $note = $this->findByUid($uid);
        return $note ? $note->delete() : false;
    }

    public function getPaginated(string $userUid, int $perPage = 15)
    {
        return $this->model->where('user_uid', $userUid)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
