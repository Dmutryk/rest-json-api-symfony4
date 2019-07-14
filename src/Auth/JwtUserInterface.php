<?php

declare(strict_types=1);

namespace App\Auth;

use Symfony\Component\Security\Core\User\UserInterface;

interface JwtUserInterface
{
    /**
     * @return UserInterface
     */
    public function get(): UserInterface;
}