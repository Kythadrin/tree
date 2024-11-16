<?php

declare(strict_types=1);

namespace App\Service;

use DI\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

class ServiceLoader
{
    public function addServices(ContainerBuilder $containerBuilder, string $servicesPath): void
    {
        $yamlConfig = Yaml::parseFile($servicesPath);

        foreach ($yamlConfig['services'] as $class => $params) {
            $arguments = $this->resolveArguments($params['arguments'] ?? []);

            $containerBuilder->addDefinitions([
                $class => \DI\create()->constructor(...$arguments)
            ]);
        }
    }

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