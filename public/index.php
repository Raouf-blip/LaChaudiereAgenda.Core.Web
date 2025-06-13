<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Chaudiere\middleware\CorsMiddleware;
use Slim\Csrf\Guard;

require __DIR__ . '/../vendor/autoload.php';

// Config session
ini_set('session.cookie_secure', '0');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
session_start();

// Chargement .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Connexion BDD
require __DIR__ . '/../src/config/database.php';

// Création app
$app = AppFactory::create();

// CSRF
$csrf = new Guard($app->getResponseFactory());
$csrf->setPersistentTokenMode(true); // optionnel mais utile en dev

// Twig
$twig = Twig::create(__DIR__ . '/../src/templates', ['cache' => false]);

// Middleware
$app->addRoutingMiddleware();
$app->add(CorsMiddleware::class);
$app->add(TwigMiddleware::create($app, $twig));
$app->add($csrf);

// Middleware pour injecter dynamiquement les tokens CSRF
$app->add(function (Request $request, $handler) use ($twig, $csrf) {
    $csrfNameKey = $csrf->getTokenNameKey();
    $csrfValueKey = $csrf->getTokenValueKey();
    $csrfName = $request->getAttribute($csrfNameKey);
    $csrfValue = $request->getAttribute($csrfValueKey);

    $twig->getEnvironment()->addGlobal('csrf', [
        'keys' => [
            'name' => $csrfNameKey,
            'value' => $csrfValueKey
        ],
        'name' => $csrfName,
        'value' => $csrfValue
    ]);

    return $handler->handle($request);
});

$app->addErrorMiddleware(true, true, true);

// OPTIONS
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

// Routes
(require __DIR__ . '/../src/config/routes/api.php')($app);
(require __DIR__ . '/../src/config/routes/admin.php')($app);

$app->run();
