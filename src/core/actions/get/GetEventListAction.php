<?php

namespace Chaudiere\core\actions\get;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Chaudiere\core\domain\repositories\EventRepository;
use Chaudiere\core\domain\repositories\CategoryRepository;

class GetEventListAction
{
    private EventRepository $eventRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(EventRepository $eventRepository, CategoryRepository $categoryRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();

        // Récupérer le paramètre de tri (par défaut 'date_asc')
        $sortBy = $queryParams['sort_by'] ?? 'date_asc';
        // Récupérer le paramètre de filtre par catégorie
        $categoryId = $queryParams['category_id'] ?? null;

        // Récupérer les événements en fonction du tri et du filtre
        if ($categoryId) {
            $events = $this->eventRepository->getEventsFilteredByCategory($categoryId, $sortBy);
        } else {
            $events = $this->eventRepository->getAllEvents($sortBy);
        }

        // Récupérer toutes les catégories pour le filtre déroulant
        $categories = $this->categoryRepository->getAllCategories();

        $view = Twig::fromRequest($request);

        // Rendre le template Twig avec les données
        return $view->render($response, 'eventList.twig', [
            'events' => $events,
            'categories' => $categories,
            'selected_category_id' => $categoryId,
            'selected_sort_by' => $sortBy,
            'page_title' => 'Gestion des Événements'
        ]);
    }
}