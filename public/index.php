<?php

use App\Bootstrap;
use App\Application;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$bootstrap = new Bootstrap();
$container = $bootstrap->initializeContainer();

$app = new Application($container);
$app->run();