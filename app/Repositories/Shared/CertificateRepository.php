<?php

namespace App\Repositories\Shared;

use App\Models\Certificate;

class CertificateRepository implements CertificateRepositoryInterface
{
    protected $model;

    public function __construct(Certificate $model)
    {
        $this->model = $model;
    }

    public function findByUserUid(string $userUid)
    {
        return $this->model->where('user_uid', $userUid)
            ->orderBy('downloaded_at', 'desc')
            ->get();
    }

    public function findByUid(string $uid)
    {
        return $this->model->where('uid', $uid)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function checkIfExists(string $userUid, string $level): bool
    {
        return $this->model->where('user_uid', $userUid)
            ->where('level', $level)
            ->exists();
    }

    public function getPaginated(string $userUid, int $perPage = 15)
    {
        return $this->model->where('user_uid', $userUid)
            ->orderBy('downloaded_at', 'desc')
            ->paginate($perPage);
    }
}
