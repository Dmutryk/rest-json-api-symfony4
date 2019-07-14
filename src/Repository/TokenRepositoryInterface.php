<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Token;

interface TokenRepositoryInterface
{
    /**
     * @param string $id
     * @return \App\Entity\Token|null
     */
    public function findById(string $id): ?Token;

    /**
     * @param string $userId
     * @return array|null
     */
    public function findByUserId(string $userId);
}