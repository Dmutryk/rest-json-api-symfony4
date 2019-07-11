<?php

declare(strict_types=1);

namespace App\Util;

use stdClass;

interface JwtUtilInterface
{
    public function encode(iterable $tokenData): string;

    public function decode(string $tokenString): stdClass;
}