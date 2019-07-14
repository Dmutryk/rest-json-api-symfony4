<?php

namespace App\Helpers;

use App\Entity\Token;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class TokenGeneratorHelper
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var JwtHelperInterface
     */
    private $jwtHelper;

    /**
     * @var string
     */
    private $jwtTimelife;

    /**
     * @var string
     */
    private $jwtRefreshTimelife;

    /**
     * TokenGeneratorHelper constructor.
     * @param EntityManagerInterface $entityManager
     * @param JwtHelperInterface $jwtHelper
     * @param string $jwtTimelife
     * @param string $jwtRefreshTimelife
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        JwtHelperInterface $jwtHelper,
        string $jwtTimelife,
        string $jwtRefreshTimelife
    ) {
        $this->entityManager = $entityManager;
        $this->jwtHelper = $jwtHelper;
        $this->jwtTimelife = $jwtTimelife;
        $this->jwtRefreshTimelife = $jwtRefreshTimelife;
    }

    /**
     * @param User $user
     * @return Token
     * @throws \Exception
     */
    public function getToken(User $user): Token
    {
        $expiresAt = (new DateTime())->modify($this->jwtTimelife)->format(DATE_ISO8601);

        return $this->generateToken($user, $expiresAt);
    }

    /**
     * @param User $user
     * @param string $tokenId
     * @return Token
     * @throws \Exception
     */
    public function getRefreshToken(User $user, string $tokenId): Token
    {
        $expiresAt = (new DateTime())->modify($this->jwtRefreshTimelife)->format(DATE_ISO8601);

        return $this->generateToken($user, $expiresAt, $tokenId);
    }

    /**
     * @param Token $token
     * @throws \Exception
     */
    public function expireToken(Token $token)
    {
        $token->setExpiresAt((new DateTime())->format(DATE_ISO8601));

        $this->entityManager->persist($token);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @param string $expiresAt
     * @param string|null $tokenId
     * @return Token
     */
    private function generateToken(User $user, string $expiresAt, string $tokenId = null): Token
    {
        $id = uniqid();
        $username = $user->getUsername();
        $userId= $user->getId();

        $tokenDataArray = [
            'id' => $id,
            'expires_at' => $expiresAt,
            'user' => [
                'name' => $username,
                'id' => $userId
            ]
        ];

        if ($tokenId !== null) {
            $tokenDataArray['token_id'] = $tokenId;
        }

        $tokenData = $this->jwtHelper->encode($tokenDataArray);

        $token = (new Token())
            ->setId($id)
            ->setUser($user)
            ->setExpiresAt($expiresAt)
            ->setData($tokenData);

        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return $token;
    }
}
