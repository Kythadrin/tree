<?php

declare(strict_types=1);

namespace App\Model\Vite;

class Manifest
{
    /**
     * @param array<string, Entrypoints> $entryPoints
     * @param array<string, mixed> $metadatas
     * @param array<int|string> $version
     */
    public function __construct(
        public string $base,
        public array $entryPoints,
        public bool $legacy,
        public array $metadatas,
        public array $version,
        public string $viteServer
    ) {
    }
}