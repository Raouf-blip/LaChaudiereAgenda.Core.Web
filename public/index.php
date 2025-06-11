<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Chaudiere\middleware\CorsMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Chargement .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Connexion BDD
require __DIR__ . '/../src/config/database.php';

// Création de l'app
$app = AppFactory::create();

$app->add(CorsMiddleware::class);

// Configuration de Twig
$twig = Twig::create(__DIR__ . '/../src/templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Middleware
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Routes
(require __DIR__ . '/../src/config/routes/api.php')($app);
(require __DIR__ . '/../src/config/routes/admin.php')($app);

$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

$app->run();