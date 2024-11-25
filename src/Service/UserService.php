<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Model\Response;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password): Response
    {
        $user = $this->userRepository->findOneByEmail($email);
        if ($user === null || !$user->verifyPassword($password)) {
            return new Response("Invalid credentials", 401);
        }

        $_SESSION['user'] = $user->getId();
        return new Response("", 201);
    }

    public function registrate(string $email, string $password): bool
    {
        if ($this->userRepository->findOneByEmail($email) !== null) {
            return false;
        }

        $user = new User($email, $password);

        $this->entityManager->persist($user);

        return true;
    }
}