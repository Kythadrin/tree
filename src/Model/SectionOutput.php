<?php

declare(strict_types=1);

namespace App\Model;

class SectionOutput
{
    public function __construct(
        public ?int $id,
        public ?string $title,
        public ?string $content,
        public ?int $parent,
    ) {}
}