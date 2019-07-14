<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Token;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TokenRepository implements TokenRepositoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $entityRepository;

    /**
     * UserRepository constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Doctrine\ORM\EntityRepository $entityRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityRepository $entityRepository
    ) {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityRepository;
    }

    /**
     * @param string $id
     * @return \App\Entity\Token|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById(string $id): ?Token
    {
        return $this->entityRepository
            ->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_SIMPLEOBJECT);
    }

    /**
     * @param string $userId
     * @return array|null
     */
    public function findByUserId(string $userId)
    {
        return $this->entityRepository
            ->createQueryBuilder('u')
            ->where('u.user = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getResult();
    }
}