<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\ViteManifestService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EntrypointExtension extends AbstractExtension
{
    public function __construct(
        private readonly ViteManifestService $viteManifestService
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_js', [$this->viteManifestService, 'getJsEntry'], ['is_safe' => ['html']]),
            new TwigFunction('vite_css', [$this->viteManifestService, 'getCssEntry'], ['is_safe' => ['html']]),
            new TwigFunction('vite_dynamic', [$this->viteManifestService, 'getDynamicEntry'], ['is_safe' => ['html']]),
        ];
    }
}