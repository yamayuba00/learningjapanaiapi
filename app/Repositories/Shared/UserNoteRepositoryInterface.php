<?php

namespace App\Repositories\Shared;

interface UserNoteRepositoryInterface
{
    public function findByUserUid(string $userUid);
    public function findByUid(string $uid);
    public function create(array $data);
    public function update(string $uid, array $data);
    public function delete(string $uid): bool;
    public function getPaginated(string $userUid, int $perPage = 15);
}
