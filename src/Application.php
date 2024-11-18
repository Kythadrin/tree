<?php

declare(strict_types=1);

namespace App;

use Psr\Container\ContainerInterface;;

class Application
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    public function run(): void
    {
        $this->initializeTwig();

        $router = $this->container->get(Router::class);

        $requestUri = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $route = $router->match($requestUri);
        if ($route !== null) {
            list($controller, $method) = $router->resolveController($route);
            $controller->$method();
        } else {
            http_response_code(404);
            echo '404 Not Found';

            error_log("404 Not Found: {$requestUri}");
        }
    }

    private function initializeTwig(): void
    {
        $twigExtensionLoader = $this->container->get('App\Service\TwigExtensionLoader');
        $twigExtensionLoader->registerExtensions();
    }
}