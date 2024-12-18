<?php

declare(strict_types=1);

namespace App;

use App\Service\TwigExtensionLoader;
use LogicException;
use Psr\Container\ContainerInterface;;

class Application
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function run(): void
    {
        $this->initializeTwig();

        /** @var Router $router */
        $router = $this->container->get(Router::class);

        $requestUri = (string) parse_url(
            isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/',
            PHP_URL_PATH
        );

        $route = $router->match(
            $requestUri,
            is_string($_SERVER['REQUEST_METHOD'])
                ? $_SERVER['REQUEST_METHOD']
                : throw new LogicException("Requested method not exist or not string")
        );
        if ($route !== null) {
            list($controller, $method, $parameters) = $router->resolveController($route);
            $controller->$method($parameters);
        } else {
            http_response_code(404);
            echo '404 Not Found';

            error_log("404 Not Found: {$requestUri}");
        }
    }

    private function initializeTwig(): void
    {
        /** @var TwigExtensionLoader $twigExtensionLoader */
        $twigExtensionLoader = $this->container->get(TwigExtensionLoader::class);
        $twigExtensionLoader->registerExtensions();
    }
}