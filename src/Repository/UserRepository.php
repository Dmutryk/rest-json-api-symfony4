<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserRepository implements UserRepositoryInterface
{
    private $entityManager;
    private $entityRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        EntityRepository $entityRepository
    ) {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityRepository;
    }

    public function findOneActiveById(string $id): ?User
    {
        return $this->entityRepository
            ->createQueryBuilder('u')
            ->where('u.id = :id')
            ->andWhere('u.isActive = true')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_SIMPLEOBJECT);
    }

    public function findOneActiveByUsername(string $username): ?User
    {
        return $this->entityRepository
            ->createQueryBuilder('u')
            ->where('u.username = :username')
            ->andWhere('u.isActive = true')
            ->setParameter('username', $username)
            ->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getOneOrNullResult(Query::HYDRATE_SIMPLEOBJECT);
    }
}