<?php

declare(strict_types=1);

namespace App\Api;

use App\Service\UserService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Encoding\Stream;

class User
{
    public function __construct(
        private readonly UserService $userService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function registration(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            /** @var string $data */
            $data = file_get_contents('php://input');

            /** @var string[] $input */
            $input = json_decode($data, true);

            $email    = $input['email'];
            $password = $input['password'];

            if ($email && $password) {
                $this->userService->registrate($email, $password);

                $this->entityManager->flush();

                http_response_code(201);
                echo json_encode([
                    'message' => 'Registration successful',
                    'data' => ['email' => $email, 'password' => $password],
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'message' => 'Invalid input, missing email or password'
                ]);
            }
        }
    }
}