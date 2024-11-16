<?php

declare(strict_types=1);

use App\Model\Routes;
use DI\Container;
use Symfony\Component\Yaml\Yaml;

class Router
{
    /** @var Routes[] $routes */
    private array $routes = [];
    private Container $container;

    public function __construct(string $yamlFile, Container $container)
    {
        $this->container = $container;
        $this->loadRoutes($yamlFile);
    }

    private function loadRoutes(string $yamlFile): void
    {
        $routes = Yaml::parseFile($yamlFile);
        foreach ($routes as $name => $route) {
            $this->routes[] = new Routes(
                $name,
                $route['path'],
                $route['controller'],
            );
        }
    }

    public function match(string $url): ?Routes
    {
        foreach ($this->routes as $route) {
            if (preg_match("~^{$route->getPath()}$~", $url)) {
                return $route;
            }
        }

        return null;
    }

    public function resolveController(Routes $route)
    {
        list($controllerClass, $method) = explode('::', $route->getController());

        $controller = $this->container->get($controllerClass);

        if (!method_exists($controller, $method)) {
            throw new LogicException("Method {$method} not found in controller {$controllerClass}.");
        }

        return [$controller, $method];
    }
}