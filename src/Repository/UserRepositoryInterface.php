<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{
    /**
     * @param string $id
     * @return \App\Entity\User|null
     */
    public function findOneActiveById(string $id): ?User;

    /**
     * @param string $username
     * @return \App\Entity\User|null
     */
    public function findOneActiveByUsername(string $username): ?User;
}