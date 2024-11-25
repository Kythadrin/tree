<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Section as SectionEntity;

class Section
{
    public function __construct(
        public ?string $title,
        public ?string $content,
        public ?SectionEntity $parent,
    ) {}
}