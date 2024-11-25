<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class RegistrationController extends AbstractController
{
    public function index(): void
    {
        $this->render('registration.html.twig', [
            'pageClass' => 'registration',
        ]);
    }
}