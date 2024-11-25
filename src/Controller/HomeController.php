<?php

declare(strict_types=1);

namespace App\Controller;

class HomeController extends AbstractController
{
    public function index(): void
    {
        $this->render('login.html.twig', [
            'pageClass' => 'login',
        ]);
    }
}