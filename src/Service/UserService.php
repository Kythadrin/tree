<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function registrate(string $email, string $password): bool
    {
        /** @var UserRepository $repository */
        $repository = $this->entityManager->getRepository(User::class);
        if ($repository->findOneByEmail($email) !== null) {
            return false;
        }

        $user = new User($email, password_hash($password, PASSWORD_DEFAULT));

        $this->entityManager->persist($user);

        return true;
    }
}