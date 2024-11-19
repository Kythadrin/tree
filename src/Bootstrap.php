<?php

declare(strict_types=1);

namespace App;

use App\Service\ServiceLoader;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class Bootstrap
{
    private const string SERVICES_CONFIG_PATH = '/config/services.yaml';
    private const string ROOT_DIR             = __DIR__ . '/../';

    public function initializeContainer(): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();

        $serviceLoader = new ServiceLoader();
        $serviceLoader->addServices($containerBuilder, self::ROOT_DIR . self::SERVICES_CONFIG_PATH);

        $containerBuilder->addDefinitions([
            'service_container' => \DI\get(ContainerInterface::class),
            'root_path' => self::ROOT_DIR,
        ]);
        $containerBuilder->addDefinitions([
            Router::class => \DI\autowire()
                ->constructorParameter('container', \DI\get(ContainerInterface::class)),
        ]);

        return $containerBuilder->build();
    }
}
