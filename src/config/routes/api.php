<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Chaudiere\core\domain\entities\Events;
use Chaudiere\core\domain\entities\Categories;
use Slim\Views\Twig;

return function($app) {

    $app->get('/', function (Request $request, Response $response) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'index.twig');
    });

// gerer les réponses JSON
    function jsonResponse(Response $response, $data, int $status = 200): Response {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*');
    }

    $app->add(function ($request, $handler) {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    });

    // liste catégories
    $app->get('/api/categories', function (Request $request, Response $response) {
        $categories = Categories::all();
        return jsonResponse($response, $categories);
    });

    // détails d'un event
    $app->get('/api/evenements/{id}', function (Request $request, Response $response, array $args) {
        $event = Events::with('category')->find($args['id']);
        if (!$event || !$event->is_published) {
            return jsonResponse($response, ['error' => 'Événement introuvable ou non publié'], 404);
        }
        return jsonResponse($response, $event);
    });

    // events publiés
    $app->get('/api/evenements', function (Request $request, Response $response) {
        $params = $request->getQueryParams();

        $query = Events::where('is_published', true)->with('category');

        // Tri 
        $sort = $params['sort'] ?? null;
        switch ($sort) {
            case 'date-asc':
                $query->orderBy('start_date', 'asc');
                break;
            case 'date-desc':
                $query->orderBy('start_date', 'desc');
                break;
            case 'titre':
                $query->orderBy('title', 'asc');
                break;
            case 'categorie':
                $query->orderBy('category_id', 'asc');
                break;
        }

        $events = $query->get()->map(function ($e) {
            return [
                'id' => $e->id,
                'title' => $e->title,
                'start_date' => $e->start_date,
                'end_date' => $e->end_date,
                'category' => $e->category->name ?? null,
                'url' => "/api/evenements/{$e->id}"
            ];
        });

        return jsonResponse($response, $events);
    });

    // events par catégorie
    $app->get('/api/categories/{id}/evenements', function (Request $request, Response $response, array $args) {
        $events = Events::where('category_id', $args['id'])
            ->where('is_published', true)
            ->with('category')
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'title' => $e->title,
                    'start_date' => $e->start_date,
                    'end_date' => $e->end_date,
                    'category' => $e->category->name ?? null,
                    'url' => "/api/evenements/{$e->id}"
                ];
            });

        return jsonResponse($response, $events);
    });
};