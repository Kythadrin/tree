<?php

declare(strict_types=1);

namespace App;

use App\Model\Routes;
use InvalidArgumentException;
use LogicException;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

class Router
{
    private const string ROUTES_CONFIG_FILE_PATH = 'config/routes.yaml';

    /** @var Routes[] */
    private array $routes = [];

    public function __construct(
        private readonly ContainerInterface $container,
    ) {
        $this->loadRoutes();
    }

    private function loadRoutes(): void
    {
        /** @var string $rootPath */
        $rootPath       = $this->container->get('root_path');
        $routesFilePath = $rootPath . self::ROUTES_CONFIG_FILE_PATH;

        if (!file_exists($routesFilePath)) {
            throw new RuntimeException("Routes configuration file not found: {$routesFilePath}");
        }

        /** @var array<string, array<string, string>> $routes */
        $routes = Yaml::parseFile($routesFilePath);

        foreach ($routes as $name => $route) {
            /** @var string $name */
            if (!isset($route['path'], $route['controller'])) {
                throw new InvalidArgumentException("Invalid route definition: {$name}");
            }

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

    /** @return array{object, string} */
    public function resolveController(Routes $route): array
    {
        list($controllerClass, $method) = explode('::', $route->getController());

        /** @var object $controller */
        $controller = $this->container->get($controllerClass);

        if (!method_exists($controller, $method)) {
            throw new LogicException("Method {$method} not found in controller {$controllerClass}.");
        }

        return [$controller, $method];
    }
}
