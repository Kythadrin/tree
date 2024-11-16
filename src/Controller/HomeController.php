<?php

declare(strict_types=1);

namespace App\Controller;

class HomeController extends AbstractController
{
    public function index(): void
    {
        $this->render('homepage/index.html.twig', [
            'title' => 'Home Page',
            'message' => 'Welcome to the home page!',
        ]);
    }
}