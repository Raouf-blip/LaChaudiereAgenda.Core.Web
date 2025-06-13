<?php

use Chaudiere\core\actions\get\GetCreateAdminUserAction;
use Chaudiere\core\actions\SigninAction;
use Chaudiere\core\actions\SignoutAction;
use Chaudiere\core\actions\SignupAction;
use Chaudiere\core\domain\repositories\UserRepository;
use Chaudiere\core\providers\SessionAuthProvider;
use Chaudiere\core\UseCase\AuthnService;
use Chaudiere\middleware\AuthMiddleware;
use Chaudiere\middleware\CorsMiddleware;
use Chaudiere\middleware\FlashMessageMiddleware;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Chargement .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Connexion BDD
require __DIR__ . '/../src/config/database.php';

$container = new Container();
AppFactory::setContainer($container);

$container->set(UserRepository::class, function ($c) {
    return new UserRepository();
});

$container->set(SessionAuthProvider::class, function ($c) {
    return new SessionAuthProvider($c->get(UserRepository::class));
});

$container->set(AuthnService::class, function ($c) {
    return new AuthnService($c->get(UserRepository::class), $c->get(SessionAuthProvider::class));
});

// Création de l'app
$app = AppFactory::create();

$app->add(CorsMiddleware::class);

// Configuration de Twig
$twig = Twig::create(__DIR__ . '/../src/templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));
$app->add(new FlashMessageMiddleware($twig));

$userRepository = new UserRepository();
$authProvider = new SessionAuthProvider($userRepository);
$authnService = new AuthnService($userRepository, $authProvider);

$container->set(GetCreateAdminUserAction::class, function ($c) {
    return new GetCreateAdminUserAction($c->get(AuthnService::class));
});

$container->set(SigninAction::class, function ($c) {
    // Correctly inject dependencies from the container
    return new SigninAction(
        $c->get(AuthnService::class),
        $c->get(SessionAuthProvider::class)
    );
});

// Dans votre index.php
$container->set(SignoutAction::class, function ($c) {
    return new SignoutAction($c->get(AuthnService::class));
});

$twig->getEnvironment()->addGlobal('auth', $authProvider);


$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$app->add(new AuthMiddleware($authProvider, [
]));

// Middleware
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Routes
(require __DIR__ . '/../src/config/routes/api.php')($app);
(require __DIR__ . '/../src/config/routes/admin.php')($app);

// Route 5 : Signin

$app->get('/signin', SigninAction::class)->setName('signin');
$app->post('/signin', SigninAction::class)->setName('signin_post');

// Route for Signout
$app->get('/signout', SignoutAction::class)->setName('signout');

// Route for Signup
$app->get('/signup', SignupAction::class)->setName('signup');
$app->post('/signup', SignupAction::class)->setName('signup_post');

// Route for Create Admin User
$app->get('/admin/create/user', GetCreateAdminUserAction::class)->setName('create_admin');
$app->post('/admin/create/user', GetCreateAdminUserAction::class)->setName('create_admin_post');

require __DIR__ . '/../src/config/routes/api.php';

$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

$app->get('/', function (Request $request, Response $response) {
    return $response->withHeader('Location', 'index.html')->withStatus(302);
})->setName('home');



$app->run();