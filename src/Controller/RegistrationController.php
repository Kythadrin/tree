<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly EntityManagerInterface $entityManager,
        Environment $twig,
    ) {
        parent::__construct($twig);
    }

    public function index(): void
    {
        $this->userService->registrate('test@mail.com', '123');
        $this->entityManager->flush();

        $this->render('registration.html.twig',
        [
            "pageClass" => 'registration',
        ]
        );
    }
}