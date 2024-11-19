<?php

declare(strict_types=1);

namespace App\Service;

use DI\ContainerBuilder;
use DI\Container;
use Symfony\Component\Yaml\Yaml;

class ServiceLoader
{
    /**
     * @param ContainerBuilder<Container> $containerBuilder
     * @param string $servicesPath
     */
    public function addServices(ContainerBuilder $containerBuilder, string $servicesPath): void
    {
        /** @var array{services: array<string, array<string, mixed>>} $yamlConfig */
        $yamlConfig = Yaml::parseFile($servicesPath);

        foreach ($yamlConfig['services'] as $class => $params) {
            /** @var array{mixed}|null $arguments */
            $arguments = $params['arguments'];
            $arguments = $this->resolveArguments($arguments ?? []);

            $containerBuilder->addDefinitions([
                $class => \DI\create()->constructor(...$arguments)
            ]);
        }
    }

    /**
     * @param array<mixed> $arguments
     * @return array<mixed>
     */
    private function resolveArguments(array $arguments): array
    {
        return array_map(function ($arg) {
            if (is_string($arg) && str_starts_with($arg, '@')) {
                $serviceId = substr($arg, 1);
                return \DI\get($serviceId);
            }
            return $arg;
        }, $arguments);
    }
}
