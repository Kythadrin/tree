<?php

declare(strict_types=1);

namespace App\Model;

class Response
{
    public function __construct(
        public string $message,
        public int $status,
    ) {
    }
}