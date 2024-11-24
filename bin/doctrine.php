<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

$container = (new App\Bootstrap())->initializeContainer();

ConsoleRunner::run(
    new SingleManagerProvider($container->get(EntityManagerInterface::class)),
);