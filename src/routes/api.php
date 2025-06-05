<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Chaudiere\entities\Events;
use Chaudiere\entities\Categories;

// gerer les réponses JSON
function jsonResponse(Response $response, $data, int $status = 200): Response {
    $response->getBody()->write(json_encode($data));
    return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Access-Control-Allow-Origin', '*');
}

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
    $events = Events::where('is_published', true)->with('category')->get()->map(function ($e) {
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
