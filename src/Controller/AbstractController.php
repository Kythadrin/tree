<?php

declare(strict_types=1);

namespace App\Controller;

use Twig\Environment;

abstract class AbstractController
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    protected function render(string $template, array $data = []): void
    {
        echo $this->twig->render($template, $data);
    }

    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }
}