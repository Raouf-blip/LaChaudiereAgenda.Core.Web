<?php
declare(strict_types=1);

namespace Chaudiere\core\domain\repositories;


use Chaudiere\core\domain\entities\User;

interface UserRepositoryInterface
{
    /**
     * Récupère un utilisateur par son user_id.
     */
    public function findByUserId(string $userId): ?User;

    /**
     * Récupère un utilisateur par son email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Récupère un utilisateur par son ID.
     * Modifié pour accepter string au lieu de int
     */
    public function findById(string $id): ?User;

    /**
     * Crée un nouvel utilisateur
     */
    public function create(array $userData): User;

    /**
     * Sauvegarde (crée ou met à jour) un utilisateur.
     */
    public function save(User $user): bool;

    /**
     * Supprime un utilisateur.
     */
    public function delete(User $user): bool;

    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists(string $email): bool;
}