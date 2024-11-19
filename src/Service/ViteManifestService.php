<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Vite\Manifest;
use DI\Container;
use LogicException;

class ViteManifestService
{
    private const string MANIFEST_PATH = '/public/build/.vite/entrypoints.json';

    private Manifest $manifest;

    public function __construct(
        private readonly Container $container,
    ) {
        $this->manifest = $this->loadManifest();
    }

    private function loadManifest(): Manifest
    {
        $rootPath = $this->container->get('root_path');
        if (!is_string($rootPath) || !file_exists($rootPath . self::MANIFEST_PATH)) {
            throw new LogicException('Manifest file not found: ' . self::MANIFEST_PATH);
        }

        $content = file_get_contents($rootPath . self::MANIFEST_PATH);
        if (!$content) {
            throw new LogicException('Failed to get manifest file data: ' . self::MANIFEST_PATH);
        }

        $manifestData = json_decode($content, false);

        if ($manifestData === null) {
            throw new LogicException('Failed to decode manifest file: ' . json_last_error_msg());
        }

        /** @var \stdClass $manifestData */
        return new Manifest(
            (string) ($manifestData->base ?? ''),
            (array) ($manifestData->entryPoints ?? []),
            (bool) ($manifestData->legacy ?? false),
            (array) ($manifestData->metadatas ?? []),
            (array) ($manifestData->version ?? []),
            (string) ($manifestData->viteServer ?? '')
        );
    }

    public function getJsEntry(string $entry): string
    {
        $file = $this->manifest->entryPoints[$entry]->js[0] ?? '';
        return $file ? "import \"$file\"" : "";
    }

    public function getCssEntry(string $entry): string
    {
        $cssPath = $this->manifest->entryPoints[$entry]->css[0] ?? '';
        return $cssPath ? sprintf('<link rel="stylesheet" href="%s">', $cssPath) : '';
    }

    public function getDynamicEntry(string $entry): string
    {
        $dynamicPath = $this->manifest->entryPoints[$entry]->dynamic[0] ?? '';
        return $dynamicPath ? sprintf('<script src="%s" type="module"></script>', $dynamicPath) : '';
    }
}
