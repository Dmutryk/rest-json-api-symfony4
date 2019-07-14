<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helpers\TokenDisablerHelper;
use App\Helpers\TokenGeneratorHelper;
use App\Repository\TokenRepository;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController
{
    /**
     * @var \App\Repository\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @var TokenGeneratorHelper
     */
    private $tokenGenerator;

    /**
     * @var TokenDisablerHelper
     */
    private $tokenDisabler;

    /**
     * LoginController constructor.
     *
     * @param \App\Repository\UserRepositoryInterface $userRepository
     * @param TokenRepository $tokenRepository
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $userPasswordEncoder
     * @param TokenGeneratorHelper $tokenGenerator
     * @param TokenDisablerHelper $tokenDisabler
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        TokenRepository $tokenRepository,
        UserPasswordEncoderInterface $userPasswordEncoder,
        TokenGeneratorHelper $tokenGenerator,
        TokenDisablerHelper $tokenDisabler

    ) {
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->tokenDisabler = $tokenDisabler;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getToken(Request $request): Response
    {
        $username = $request->get('username');

        if (null === $username) {
            throw new UnauthorizedHttpException('API Login', "Username not found.");
        }

        $user = $this->userRepository->findOneActiveByUsername($username);

        if (null === $user) {
            throw new UnauthorizedHttpException('API Login',"This user doesn't exist.");
        }

        $password = $request->get('password');
        if (!$this->userPasswordEncoder->isPasswordValid($user, $password)) {
            throw new UnauthorizedHttpException('API Login','Invalid credentials.');
        }

        $oldTokens = $this->tokenRepository->findByUserId($user->getId());

        //clear previous tokens
        $this->tokenDisabler->disableTokens($oldTokens);

        $token = $this->tokenGenerator->getToken($user);
        $refreshToken = $this->tokenGenerator->getRefreshToken($user, $token->getId());

        $responseMessage = [
            'token' => $token->getData(),
            'refresh_token' => $refreshToken->getData(),
        ];

        return new JsonResponse($responseMessage,Response::HTTP_ACCEPTED);
    }
}
