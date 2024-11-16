<?php

declare(strict_types=1);

namespace App\Service;

use LogicException;

class ViteManifestService
{
    private string $manifestPath;
    private array $manifest;

    public function __construct(string $manifestPath)
    {
        $this->manifestPath = $manifestPath;
        $this->manifest = $this->loadManifest();
    }

    private function loadManifest(): array
    {
        if (!file_exists($this->manifestPath)) {
            throw new LogicException("Manifest file not found: {$this->manifestPath}");
        }

        $content = file_get_contents($this->manifestPath);
        return json_decode($content, true);
    }

    public function getJsEntry(string $entry): string
    {
        $file = $this->manifest['entryPoints'][$entry]['js'][0];
        return $file ? "import \"$file\"" : "";
    }

    public function getCssEntry(string $entry): string
    {
        $cssPath = $this->manifest['entryPoints'][$entry]['css'][0] ?? '';
        return $cssPath ? sprintf('<link rel="stylesheet" href="%s">', $cssPath) : '';
    }

    public function getDynamicEntry(string $entry): string
    {
        $dynamicPath = $this->manifest['entryPoints'][$entry]['dynamic'][0] ?? '';
        return $dynamicPath ? sprintf('<script src="%s" type="module"></script>', $dynamicPath) : '';
    }
}
