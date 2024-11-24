<?php

declare(strict_types=1);

namespace App\Controller;

use Twig\Environment;

class HomeController extends AbstractController
{
    public function __construct(
        Environment $twig,
    ) {
        parent::__construct($twig);
    }

    public function index(): void
    {
        $this->render('homepage/index.html.twig', [
            'title' => 'Home Page',
            'message' => 'Welcome to the home page!',
        ]);
    }
}