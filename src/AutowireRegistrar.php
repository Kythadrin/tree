<?php

declare(strict_types=1);

namespace App;

use DI\Container;
use DI\ContainerBuilder;
use LogicException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

class AutowireRegistrar
{
    /**
     * @param ContainerBuilder<Container> $containerBuilder
     * @param string[] $excludePaths
     */
    public static function autowireServices(
        ContainerBuilder $containerBuilder,
        string $baseNamespace,
        string $basePath,
        array $excludePaths
    ): void {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $relativePath = str_replace([$basePath, '/', '.php'], ['', '\\', ''], $file->getRealPath());
                $className    = $baseNamespace . $relativePath;

                foreach ($excludePaths as $exclude) {
                    $path = $file->getRealPath();
                    if (!$path) {
                        throw new LogicException('Can\'t get class path');
                    }
                    $realExcludePath = realpath($exclude);
                    if (!$realExcludePath) {
                        throw new LogicException("Can't get real path of excluded class: $exclude");
                    }

                    if (str_starts_with($path, $realExcludePath)) {
                        continue 2;
                    }
                }

                if (class_exists($className)) {
                    $reflection = new ReflectionClass($className);

                    if ($reflection->isInstantiable()) {
                        $containerBuilder->addDefinitions([
                            $className => \DI\autowire(),
                        ]);
                    }
                }
            }
        }
    }
}