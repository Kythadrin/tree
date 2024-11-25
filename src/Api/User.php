<?php

declare(strict_types=1);

namespace App\Api;

use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class User
{
    public function __construct(
        private readonly UserService $userService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            /** @var string $data */
            $data = file_get_contents('php://input');

            /** @var string[] $input */
            $input = json_decode($data, true);

            $email    = trim($input['email']);
            $password = trim($input['password']);

            if (empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode(['message' => 'Email and password are required']);
                return;
            }

            $response = $this->userService->login($email, $password);

            http_response_code($response->status);
            echo json_encode([
                'message' => $response->message,
            ]);
        }
    }

    public function registration(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            /** @var string $data */
            $data = file_get_contents('php://input');

            /** @var string[] $input */
            $input = json_decode($data, true);

            $email    = trim($input['email']);
            $password = trim($input['password']);

            if (empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode(['message' => 'Email and password are required']);
                return;
            }

            try {
                if (!$this->userService->registrate($email, $password)) {
                    http_response_code(409);
                    echo json_encode([
                        'message' => 'User with this email already exist',
                    ]);
                    return;
                }

                $this->entityManager->flush();
            } catch (Exception $exception) {
                http_response_code(500);
                echo json_encode([
                    'message' => $exception->getMessage(),
                ]);
                return;
            }


            http_response_code(201);
        }
    }
}