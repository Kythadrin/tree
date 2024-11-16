<?php

declare(strict_types=1);

namespace App\Model;

class Routes
{
    private string $name;
    private string $path;
    private string $controller;

    public function __construct(
        string $name,
        string $path,
        string $controller,
    ) {
        $this->name = $name;
        $this->path = $path;
        $this->controller = $controller;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }
}