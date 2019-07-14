<?php

declare(strict_types=1);

namespace App\Helpers;

use Firebase\JWT\JWT;
use stdClass;

class JwtHelper implements JwtHelperInterface
{
    /**
     * @var string
     */
    private $jwtAlgorithm;

    /**
     * @var string
     */
    private $jwtPrivateKey;

    /**
     * @var string
     */
    private $jwtPublicKey;

    /**
     * JwtHelper constructor.
     *
     * @param string $jwtAlgorithm
     * @param string $jwtPrivateKey
     * @param string $jwtPublicKey
     */
    public function __construct(
        string $jwtAlgorithm,
        string $jwtPrivateKey,
        string $jwtPublicKey
    ) {
        $this->jwtAlgorithm = $jwtAlgorithm;
        $this->jwtPrivateKey = $jwtPrivateKey;
        $this->jwtPublicKey = $jwtPublicKey;
    }

    /**
     * @param iterable $tokenData
     * @return string
     */
    public function encode(iterable $tokenData): string
    {
        return JWT::encode($tokenData, file_get_contents($this->jwtPrivateKey), $this->jwtAlgorithm);
    }

    /**
     * @param string $tokenString
     * @return stdClass
     */
    public function decode(string $tokenString): stdClass
    {
        return JWT::decode($tokenString, file_get_contents($this->jwtPublicKey), [$this->jwtAlgorithm]);
    }
}