<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;

interface JwtUserInterface
{
    public function get(): User;
}