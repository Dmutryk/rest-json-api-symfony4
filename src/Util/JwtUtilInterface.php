<?php

declare(strict_types=1);

namespace App\Util;

use stdClass;

interface JwtUtilInterface
{
    /**
     * @param iterable $tokenData
     * @return string
     */
    public function encode(iterable $tokenData): string;

    /**
     * @param string $tokenString
     * @return \stdClass
     */
    public function decode(string $tokenString): stdClass;
}