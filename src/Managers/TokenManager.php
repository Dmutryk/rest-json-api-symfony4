<?php

namespace App\Managers;

use App\Entity\Token;
use Doctrine\ORM\EntityManagerInterface;

class TokenManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TokenManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Token $token
     */
    public function delete(Token $token)
    {
        $this->entityManager->remove($token);
        $this->entityManager->flush();
    }
}
