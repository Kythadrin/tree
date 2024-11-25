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

    /** @param array{item?: string} $data */
    protected function render(string $template, array $data = []): void
    {
        echo $this->twig->render($template, $data);
    }

    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit();
    }
}