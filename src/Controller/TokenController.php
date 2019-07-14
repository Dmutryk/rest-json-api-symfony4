<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Helpers\ObjectToArray;
use App\Helpers\TokenDisablerHelper;
use App\Helpers\TokenGeneratorHelper;
use App\Repository\TokenRepository;
use App\Repository\UserRepositoryInterface;
use App\Validators\TokenValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TokenController
{
    /**
     * @var \App\Repository\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var TokenValidator
     */
    private $tokenValidator;

    /**
     * @var TokenGeneratorHelper
     */
    private $tokenGenerator;

    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * @var TokenDisablerHelper
     */
    private $tokenDisabler;

    /**
     * LoginController constructor.
     *
     * @param \App\Repository\UserRepositoryInterface $userRepository
     * @param TokenValidator $tokenValidator
     * @param TokenGeneratorHelper $tokenGenerator
     * @param TokenRepository $tokenRepository
     * @param TokenDisablerHelper $tokenDisabler
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        TokenValidator $tokenValidator,
        TokenGeneratorHelper $tokenGenerator,
        TokenRepository $tokenRepository,
        TokenDisablerHelper $tokenDisabler

    ) {
        $this->userRepository = $userRepository;
        $this->tokenValidator = $tokenValidator;
        $this->tokenGenerator = $tokenGenerator;
        $this->tokenRepository = $tokenRepository;
        $this->tokenDisabler = $tokenDisabler;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function refreshTokens(Request $request): Response
    {
        $refreshToken = $request->get('refresh_token');

        if (null === $refreshToken) {
            return new Response(
                'refresh_token not found in request!',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $refreshTokenData = $this->tokenValidator->validateRefreshToken($refreshToken);
        } catch (\Exception $exception) {
            return new Response($exception->getMessage(),Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $this->getUserByUsername($refreshTokenData->user->name);
        $tokenId = $refreshTokenData->token_id;
        $refreshTokenId = $refreshTokenData->id;


        $token = $this->tokenRepository->findById($tokenId);
        $refreshToken = $this->tokenRepository->findById($refreshTokenId);

        //remove old tokens
        $this->tokenDisabler->disableTokens([$token, $refreshToken]);

        //generate new tokens
        $token = $this->tokenGenerator->getToken($user);
        $refreshToken = $this->tokenGenerator->getRefreshToken($user, $token->getId());

        $responseMessage = [
            'token' => $token->getData(),
            'refresh_token' => $refreshToken->getData(),
        ];

        return new JsonResponse($responseMessage,Response::HTTP_ACCEPTED);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function disableTokens(Request $request): Response
    {
        $refreshToken = $request->get('refresh_token');

        if (null === $refreshToken) {
            return new Response(
                'refresh_token not found in request!',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $refreshTokenData = $this->tokenValidator->validateRefreshToken($refreshToken);
        } catch (\Exception $exception) {
            return new Response($exception->getMessage(),Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tokenId = $refreshTokenData->token_id;
        $refreshTokenId = $refreshTokenData->id;

        $token = $this->tokenRepository->findById($tokenId);
        $refreshToken = $this->tokenRepository->findById($refreshTokenId);

        //remove old tokens
        $this->tokenDisabler->disableTokens([$token, $refreshToken]);

        return new Response('Tokens were disabled successfully!',Response::HTTP_OK);
    }

    /**
     * @param string $username
     * @return User|null
     */
    private function getUserByUsername(string $username)
    {
        $user = $this->userRepository->findOneActiveByUsername($username);

        if (null === $user) {
            throw new UnauthorizedHttpException('API Login',"This user doesn't exist.");
        }

        return $user;
    }
}
