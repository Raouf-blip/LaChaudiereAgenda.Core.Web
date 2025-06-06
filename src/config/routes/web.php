<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

$app->get('/', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'home.twig');
});

$app->get('/calendar', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'calendar.twig');
});

$app->get('/events', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'events.twig');
});