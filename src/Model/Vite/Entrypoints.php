<?php

declare(strict_types=1);

namespace App\Model\Vite;

class Entrypoints
{
    /**
     * @param array<int, string> $css
     * @param array<int, string> $dynamic
     * @param array<int, string> $js
     * @param array<int, string> $preload
     */
    public function __construct(
        public array $css,
        public array $dynamic,
        public array $js,
        public bool $legacy,
        public array $preload,
    ) {
    }
}