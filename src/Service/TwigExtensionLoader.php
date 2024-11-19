<?php

declare(strict_types=1);

namespace App\Service;

use ReflectionClass;
use ReflectionNamedType;
use Twig\Environment as TwigEnvironment;
use LogicException;
use Symfony\Component\Yaml\Yaml;
use Psr\Container\ContainerInterface;
use Twig\Extension\ExtensionInterface;

class TwigExtensionLoader
{
    public function __construct(
        private readonly TwigEnvironment $twig,
        private readonly string $projectDir,
        private readonly ContainerInterface $container,
    ) {
    }

    /** @throws LogicException */
    public function registerExtensions(): void
    {
        $configPath = $this->projectDir . '/config/twig_extensions.yaml';

        if (!file_exists($configPath)) {
            throw new LogicException("Twig extensions configuration file not found at $configPath.");
        }

        /** @var array{extensions: array<string, array<string, string>>} $twigConfig */
        $twigConfig = Yaml::parseFile($configPath);
        foreach ($twigConfig['extensions'] as $extensionClass => $params) {
            if (!class_exists($extensionClass)) {
                throw new LogicException("Class $extensionClass not found.\n");
            }

            $extension = $this->instantiateExtension($extensionClass, $params);
            $this->twig->addExtension($extension);
        }
    }

    /**
     * @param string $extensionClass
     * @param array<string, string> $params
     * @return ExtensionInterface
     */
    private function instantiateExtension(string $extensionClass, array $params): ExtensionInterface
    {
        if (!class_exists($extensionClass)) {
            throw new LogicException("Class $extensionClass does not exist.");
        }

        $reflectionClass = new ReflectionClass($extensionClass);

        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            /** @var ExtensionInterface $extension */
            $extension = new $extensionClass();
            return $extension;
        }

        $parameters = $constructor->getParameters();
        $args       = [];
        foreach ($parameters as $parameter) {
            $paramType = $parameter->getType();

            if ($paramType instanceof ReflectionNamedType) {
                $paramClass = $paramType->getName();
            } else {
                $paramClass = null;
            }

            if ($paramClass && isset($params[$parameter->getName()])) {
                $serviceId = substr($params[$parameter->getName()], 1);
                if (!$this->container->has($serviceId)) {
                    throw new LogicException("Service '{$serviceId}' not found in the container.");
                }

                $args[] = $this->container->get($serviceId);
            } elseif ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            } else {
                throw new LogicException("Could not resolve parameter {$parameter->getName()} for {$extensionClass}");
            }
        }

        /** @var ExtensionInterface $extension */
        $extension = $reflectionClass->newInstanceArgs($args);

        return $extension;
    }
}
