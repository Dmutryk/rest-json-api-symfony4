<?php

namespace App\Validators;

use App\Helpers\JwtHelperInterface;
use App\Repository\TokenRepository;
use DateTime;
use Exception;
use stdClass;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class TokenValidator
{
    /**
     * @var JwtHelperInterface
     */
    private $jwtHelper;

    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * TokenValidator constructor.
     * @param JwtHelperInterface $jwtHelper
     * @param TokenRepository $tokenRepository
     */
    public function __construct(JwtHelperInterface $jwtHelper, TokenRepository $tokenRepository)
    {
        $this->jwtHelper = $jwtHelper;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @param string $token
     * @return stdClass
     * @throws Exception
     */
    public function validateRefreshToken(string $token): stdClass
    {
        try {
            $tokenData = $this->jwtHelper->decode($token);
        } catch (Exception $e) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        if (!property_exists($tokenData, 'id')) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        if (!property_exists($tokenData, 'token_id')) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        if (!property_exists($tokenData, 'user')) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        $isTokenExist = $this->tokenRepository->findById($tokenData->id);
        if (!$isTokenExist) {
            throw new CustomUserMessageAuthenticationException('Not existed token.');
        }

        $expiresAt = new DateTime($tokenData->expires_at);
        if ($expiresAt < new DateTime()) {
            throw new CustomUserMessageAuthenticationException('Token expired.');
        }

        return $tokenData;
    }
}