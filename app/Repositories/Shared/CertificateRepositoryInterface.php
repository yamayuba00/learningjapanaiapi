<?php

namespace App\Repositories\Shared;

interface CertificateRepositoryInterface
{
    public function findByUserUid(string $userUid);
    public function findByUid(string $uid);
    public function create(array $data);
    public function checkIfExists(string $userUid, string $level): bool;
    public function getPaginated(string $userUid, int $perPage = 15);
}
