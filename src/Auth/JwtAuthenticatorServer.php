<?php

declare(strict_types=1);

namespace App\Auth;

use App\Entity\User;
use App\Helpers\JwtHelperInterface;
use App\Repository\TokenRepository;
use DateTime;
use Exception;
use InvalidArgumentException;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class JwtAuthenticatorServer implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var \App\Helpers\JwtHelperInterface
     */
    private $jwtHelper;

    /**
     * @var TokenRepository
     */
    private $tokenRepository;
    /**
     * JwtAuthenticatorServer constructor.
     *
     * @param \App\Helpers\JwtHelperInterface $jwtHelper
     * @param TokenRepository $tokenRepository
     */
    public function __construct(JwtHelperInterface $jwtHelper, TokenRepository $tokenRepository)
    {
        $this->jwtHelper = $jwtHelper;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $providerKey
     * @return \Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey)
    {
        $token = $request->headers->get('Authorization');
        if (!$token) {
            throw new CustomUserMessageAuthenticationException('Missing token.');
        }

        return new PreAuthenticatedToken('anon.', $token, $providerKey);
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
     * @param $providerKey
     * @return \Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken
     * @throws \Exception
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof UserProviderInterface) {
            throw new InvalidArgumentException('Invalid provider.');
        }

        $tokenData = $this->validateToken($token);

        $user = $userProvider->loadUserByUsername($tokenData->user->name);
        if (!$user instanceof User) {
            throw new CustomUserMessageAuthenticationException('User not found.');
        }

        return new PreAuthenticatedToken($user, $user->getUsername(), $providerKey, $user->getRoles());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response($exception->getMessageKey(), Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return \stdClass
     * @throws \Exception
     */
    private function validateToken(TokenInterface $token): stdClass
    {
        preg_match('/^Bearer\s(\S+)/i', $token->getCredentials(), $matches);
        if (!$matches) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        try {
            $tokenData = $this->jwtHelper->decode($matches[1]);
        } catch (Exception $e) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        $isTokenExist = $this->tokenRepository->findById($tokenData->id);
        if (!$isTokenExist) {
            throw new CustomUserMessageAuthenticationException('Not existed token.');
        }

        $isRefreshToken = $tokenData->token_id ?? false;
        if ($isRefreshToken) {
            throw new CustomUserMessageAuthenticationException('Use general token instead of refresh.');
        }

        $expiresAt = new DateTime($tokenData->expires_at);
        if ($expiresAt < new DateTime()) {
            throw new CustomUserMessageAuthenticationException('Token expired.');
        }

        return $tokenData;
    }
}