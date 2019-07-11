<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{
    public function findOneActiveById(string $id): ?User;

    public function findOneActiveByUsername(string $username): ?User;
}