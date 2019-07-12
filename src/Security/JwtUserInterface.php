<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;

interface JwtUserInterface
{
    /**
     * @return \App\Entity\User
     */
    public function get(): User;
}