<?php

use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Chaudiere\controllers\AdminController;

return function (App $app) {
    $app->map(['GET', 'POST'], '/admin/create/event', AdminController::class . ':createEvent');
    $app->map(['GET', 'POST'], '/admin/create/category', AdminController::class . ':createCategory');
};