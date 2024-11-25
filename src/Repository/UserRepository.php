<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use App\Entity\User;
use Doctrine\ORM\Mapping\ClassMetadata;

/** @extends EntityRepository<User> */
class UserRepository extends EntityRepository
{
    public function __construct(
        EntityManagerInterface $entityManager,
        ClassMetadata $class,
    ) {
        parent::__construct($entityManager, $class);
    }

    public function findOneByEmail(string $email): ?User
    {
        /** @var ?User $user */
        $user = $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $user;
    }
}