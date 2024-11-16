<?php

declare(strict_types=1);

namespace App\Service;

use ReflectionClass;
use Twig\Environment as TwigEnvironment;
use LogicException;
use Symfony\Component\Yaml\Yaml;
use Psr\Container\ContainerInterface;

class TwigExtensionLoader
{
    public function __construct(
        private readonly TwigEnvironment $twig,
        private readonly string $projectDir,
        private readonly ContainerInterface $container,
    ) {
    }

    public function registerExtensions(): void
    {
        $configPath = $this->projectDir . '/config/twig_extensions.yaml';

        if (!file_exists($configPath)) {
            throw new LogicException("Twig extensions configuration file not found at $configPath.");
        }

        $twigConfig = Yaml::parseFile($configPath);

        if (isset($twigConfig['extensions']) && is_array($twigConfig['extensions'])) {
            foreach ($twigConfig['extensions'] as $extensionClass => $params) {
                if (class_exists($extensionClass)) {
                    $extension = $this->instantiateExtension($extensionClass, $params);

                    $this->twig->addExtension($extension);
                } else {
                    throw new LogicException("Class $extensionClass not found.\n");
                }
            }
        } else {
            throw new LogicException("No valid 'extensions' key found in $configPath.");
        }
    }

    private function instantiateExtension(string $extensionClass, array $params)
    {
        $reflectionClass = new ReflectionClass($extensionClass);

        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return new $extensionClass();
        }

        $parameters = $constructor->getParameters();
        $args = [];

        foreach ($parameters as $parameter) {
            $paramClass = $parameter->getType() && !$parameter->getType()->isBuiltin()
                ? $parameter->getType()->getName()
                : null;

            if ($paramClass && isset($params[$parameter->getName()])) {
                $serviceId = substr($params[$parameter->getName()], 1);
                if ($this->container->has($serviceId)) {
                    $args[] = $this->container->get($serviceId);
                } else {
                    throw new LogicException("Service '{$serviceId}' not found in the container.");
                }
            } elseif ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            } else {
                throw new LogicException("Could not resolve parameter {$parameter->getName()} for {$extensionClass}");
            }
        }

        return $reflectionClass->newInstanceArgs($args);
    }
}
