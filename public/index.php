<?php

use App\Service\ServiceLoader;
use App\Service\TwigExtensionLoader;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

$rootDir = __DIR__ . '/../';

require_once $rootDir . '/vendor/autoload.php';
require_once $rootDir . '/src/Router.php';

$containerBuilder = new ContainerBuilder();
$serviceLoader = new ServiceLoader();
$serviceLoader->addServices($containerBuilder, $rootDir . '/config/services.yaml');
$containerBuilder->addDefinitions([
    'service_container' => \DI\get(ContainerInterface::class),
]);
$container = $containerBuilder->build();

$twigExtensionLoader = $container->get(TwigExtensionLoader::class);
$twigExtensionLoader->registerExtensions();

$router = new Router($rootDir . '/config/routes.yaml', $container);

$requestUri = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = (string) $_SERVER['REQUEST_METHOD'];

$route = $router->match($requestUri, $requestMethod);

if ($route !== null) {
    list($controller, $method) = $router->resolveController($route);

    $controller->$method();
} else {
    http_response_code(404);
    echo '404 Not Found';
}