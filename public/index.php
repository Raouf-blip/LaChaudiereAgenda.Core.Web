<?php

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require __DIR__ . '/../src/config/database.php';

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$twig = Twig::create(__DIR__ . '/../src/web/templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

require __DIR__ . '/../src/config/routes/web.php';
require __DIR__ . '/../src/config/routes/api.php';

$app->run();
