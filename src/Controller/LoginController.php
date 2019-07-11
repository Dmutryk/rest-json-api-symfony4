<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Util\JwtUtilInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController
{
    private $userRepository;
    private $entityManager;
    private $userPasswordEncoder;
    private $jwtUtil;
    private $jwtTtl;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        JwtUtilInterface $jwtUtil,
        string $jwtTtl
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->jwtUtil = $jwtUtil;
        $this->jwtTtl = $jwtTtl;
    }

    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->userRepository->findOneActiveByUsername($data['username']);
        if (
            !$user instanceof User ||
            !$this->userPasswordEncoder->isPasswordValid($user, $data['password'])
        ) {
            throw new UnauthorizedHttpException('Basic realm="API Login"', 'Invalid credentials.');
        }

        $id = Uuid::uuid4()->toString();
        $createdAt = (new DateTime())->format(DATE_ISO8601);
        $expiresAt = (new DateTime())->modify($this->jwtTtl)->format(DATE_ISO8601);

        $tokenData = [
            'id' => $id,
            'created_at' => $createdAt,
            'expires_at' => $expiresAt,
            'user' => [
                'id' => $user->getId(),
                'roles' => $user->getRoles(),
            ],
        ];

        $token = new Token();
        $token->setId($id);
        $token->setCreatedAt($createdAt);
        $token->setExpiresAt($expiresAt);
        $token->setUser($user);
        $token->setData($this->jwtUtil->encode($tokenData));

        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return new Response($token->getData(), Response::HTTP_CREATED);
    }
}
