<?php

declare(strict_types=1);

namespace App\Model;

class Routes
{
    /** @param string[] $parameters */
    public function __construct(
        private string $name,
        private string $path,
        private string $controller,
        private string $method,
        private array $parameters = [],
    ) {
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

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /** @param string[] $parameters */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /** @return string[] */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}