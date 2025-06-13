<?php

use Chaudiere\core\domain\repositories\UserRepository;
use Chaudiere\core\providers\SessionAuthProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Chaudiere\core\domain\entities\Events;
use Chaudiere\core\domain\entities\Categories;
use Slim\Views\Twig;

return function($app) {

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
        $event = Events::with('category', 'image')->find($args['id']);
        if (!$event || !$event->is_published) {
            return jsonResponse($response, ['error' => 'Événement introuvable ou non publié'], 404);
        }
        return jsonResponse($response, $event);
    });

    // events publiés
    $app->get('/api/evenements', function (Request $request, Response $response) {
        $params = $request->getQueryParams();

        $query = Events::where('is_published', true)->with('category', 'image');

        $sort = $params['sort'] ?? null;

        // If no sort parameter is present, filter for the current month.
        if (!$sort) {
            $query->whereMonth('start_date', date('m'))
                  ->whereYear('start_date', date('Y'));
        }

        // Tri
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
                'artist' => $e->artist,
                'start_date' => $e->start_date,
                'end_date' => $e->end_date,
                'category' => $e->category->name ?? null,
                'image' => $e->image->name ?? null,
                'url' => "/api/evenements/{$e->id}"
            ];
        });

        return jsonResponse($response, $events);
    });

    // events par catégorie
    $app->get('/api/categories/{id}/evenements', function (Request $request, Response $response, array $args) {
        $events = Events::where('category_id', $args['id'])
            ->where('is_published', true)
            ->with('category', 'image')
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'title' => $e->title,
                    'artist' => $e->artist,
                    'start_date' => $e->start_date,
                    'end_date' => $e->end_date,
                    'category' => $e->category->name ?? null,
                    'image' => $e->image->name ?? null,
                    'url' => "/api/evenements/{$e->id}"
                ];
            });

        return jsonResponse($response, $events);
    });

    $app->get('/api/role', function (Request $request, Response $response) {
        $userRepo = new UserRepository();
        $auth = new SessionAuthProvider($userRepo); // ✅ correction ici

        $role = $auth->getUserRole() ?? 0;

        $response->getBody()->write((string)$role);
        return $response->withHeader('Content-Type', 'text/plain');
    });
};