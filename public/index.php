<?php

use App\Bootstrap;
use App\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$bootstrap = new Bootstrap();
$container = $bootstrap->initializeContainer();

$app = new Application($container);
$app->run();