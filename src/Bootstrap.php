<?php

declare(strict_types=1);

namespace App;

use App\Service\TwigExtensionLoader;
use App\Service\UserService;
use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\ORMSetup;
use Dotenv\Dotenv;
use LogicException;
use Psr\Container\ContainerInterface;
use function DI\string;

class Bootstrap
{
    private const string ROOT_DIR      = __DIR__ . '/../';
    private const string SRC_PATH      = __DIR__;
    private const string SRC_NAMESPACE = 'App';

    public function initializeContainer(): ContainerInterface
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addDefinitions([
            'service_container' => \DI\get(ContainerInterface::class),
            'root_path' => self::ROOT_DIR,
            \Twig\Environment::class => function () {
                $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
                return new \Twig\Environment($loader);
            },
            TwigExtensionLoader::class => \DI\autowire()
                ->constructorParameter('twig', \DI\get(\Twig\Environment::class))
                ->constructorParameter('container', \DI\get(ContainerInterface::class)),
            EntityManagerInterface::class => function () {
                $config = ORMSetup::createAttributeMetadataConfiguration(
                    [__DIR__ . '/../src/Entity'],
                    true,
                );

                $connectionParams = [
                    'driver' => 'pgsql',
                    'dbname' => is_string($_ENV['POSTGRES_DB']) ? $_ENV['POSTGRES_DB'] : throw new LogicException('POSTGRES_DB is not set or is not a string'),
                    'user' => is_string($_ENV['POSTGRES_USER']) ? $_ENV['POSTGRES_USER'] : throw new LogicException('POSTGRES_DB is not set or is not a string'),
                    'password' => is_string($_ENV['POSTGRES_PASSWORD']) ? $_ENV['POSTGRES_PASSWORD'] : throw new LogicException('POSTGRES_DB is not set or is not a string'),
                    'host' => is_string($_ENV['POSTGRES_HOST']) ? $_ENV['POSTGRES_HOST'] : throw new LogicException('POSTGRES_DB is not set or is not a string'),
                ];
                $connection = DriverManager::getConnection($connectionParams, $config);

                return new EntityManager($connection, $config);
            },
        ]);

        AutowireRegistrar::autowireServices(
            $containerBuilder,
            self::SRC_NAMESPACE,
            self::SRC_PATH,
            [
                self::SRC_PATH . '/Entity/',
                self::SRC_PATH . '/Application.php',
                self::SRC_PATH . '/AutowireRegistrar.php',
                self::SRC_PATH . '/Bootstrap.php',
            ]
        );

        return $containerBuilder->build();
    }
}
