<?php

namespace Chaudiere\core\domain\repositories;

use Chaudiere\core\domain\entities\Categories; // Use your Eloquent model
use Illuminate\Support\Collection;

class CategoryRepository
{
    /**
     * Récupère toutes les catégories.
     *
     * @return Collection Collection of Categories.
     */
    public function getAllCategories(): Collection
    {
        return Categories::orderBy('name', 'asc')->get();
    }

    /**
     * Récupère une catégorie par son ID.
     *
     * @param int $id L'ID de la catégorie.
     * @return Categories|null L'objet Category si trouvé, null sinon.
     */
    public function getCategoryById(int $id): ?Categories
    {
        return Categories::find($id);
    }

    /**
     * Ajoute une nouvelle catégorie.
     *
     * @param array $categoryData Array of category data.
     * @return Categories The newly created Category.
     */
    public function addCategory(array $categoryData): Categories
    {
        return Categories::create($categoryData);
    }

    /**
     * Met à jour une catégorie existante.
     *
     * @param int $id The ID of the category to update.
     * @param array $categoryData Array of category data.
     * @return bool True if the update succeeds, false otherwise.
     */
    public function updateCategory(int $id, array $categoryData): bool
    {
        $category = Categories::find($id);
        if ($category) {
            return $category->update($categoryData);
        }
        return false;
    }

    /**
     * Supprime une catégorie.
     *
     * @param int $id L'ID de la catégorie à supprimer.
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function deleteCategory(int $id): bool
    {
        return Categories::destroy($id) > 0;
    }
}