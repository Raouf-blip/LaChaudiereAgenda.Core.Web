<?php

namespace Chaudiere\core\domain\repositories;

use Chaudiere\core\domain\entities\Events;
use Illuminate\Support\Collection;

class EventRepository
{
    /**
     * Récupère tous les événements avec des options de tri.
     *
     * @param string $sortBy Critère de tri ('date_asc', 'date_desc', 'title_asc').
     * @return Collection Collection of Events.
     */
    public function getAllEvents(string $sortBy = 'date_asc'): Collection
    {
        $query = Events::with('category'); // Eager load the category relationship

        switch ($sortBy) {
            case 'date_desc':
                $query->orderBy('start_date', 'desc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'date_asc': // Default
            default:
                $query->orderBy('start_date', 'asc');
                break;
        }

        return $query->get();
    }

    /**
     * Récupère les événements filtrés par catégorie et triés.
     *
     * @param int $categoryId L'ID de la catégorie à filtrer.
     * @param string $sortBy Critère de tri.
     * @return Collection Collection of Events.
     */
    public function getEventsFilteredByCategory(int $categoryId, string $sortBy = 'date_asc'): Collection
    {
        $query = Events::with('category')
            ->where('category_id', $categoryId);

        switch ($sortBy) {
            case 'date_desc':
                $query->orderBy('start_date', 'desc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'date_asc': // Default
            default:
                $query->orderBy('start_date', 'asc');
                break;
        }

        return $query->get();
    }

    /**
     * Récupère un événement par son ID.
     *
     * @param int $id L'ID de l'événement.
     * @return Events|null L'objet Event si trouvé, null sinon.
     */
    public function getEventById(int $id): ?Events
    {
        return Events::with('category')->find($id);
    }

    /**
     * Ajoute un nouvel événement dans la base de données.
     *
     * @param array $eventData Array of event data.
     * @return Events The newly created Event.
     */
    public function addEvent(array $eventData): Events
    {
        return Events::create($eventData);
    }

    /**
     * Met à jour un événement existant dans la base de données.
     *
     * @param int $id The ID of the event to update.
     * @param array $eventData Array of event data.
     * @return bool True if the update succeeds, false otherwise.
     */
    public function updateEvent(int $id, array $eventData): bool
    {
        $event = Events::find($id);
        if ($event) {
            return $event->update($eventData);
        }
        return false;
    }

    /**
     * Supprime un événement de la base de données.
     *
     * @param int $id L'ID de l'événement à supprimer.
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function deleteEvent(int $id): bool
    {
        return Events::destroy($id) > 0;
    }
}