<?php
declare(strict_types=1);

namespace Chaudiere\core\domain\repositories;

use Chaudiere\core\domain\entities\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Récupère un utilisateur par son email.
     */
    public function findByUserId(string $userId): ?User
    {
        return User::where('email', $userId)->first();
    }

    /**
     * Récupère un utilisateur par son email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Récupère un utilisateur par son ID.
     */
    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    /**
     * Crée un nouvel utilisateur avec ID généré automatiquement
     */
    public function create(array $userData): User
    {
        $user = new User();

        $user->email = $userData['email'] ?? $userData['email'];
        $user->password = $userData['password'];
        $user->role = $userData['role'] ?? User::ROLE_USER;

        // Ajouter nom et prenom si disponibles
        if (isset($userData['nom'])) {
            $user->nom = $userData['nom'];
        }
        if (isset($userData['prenom'])) {
            $user->prenom = $userData['prenom'];
        }

        $user->save();

        return $user;
    }

    /**
     * Sauvegarde (crée ou met à jour) un utilisateur.
     */
    public function save(User $user): bool
    {
        return $user->save();
    }

    /**
     * Supprime un utilisateur.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Vérifie si un email/email existe déjà
     */
    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Récupère tous les utilisateurs (pour la gestion par le super-admin)
     */
    public function findAll(): array
    {
        return User::all()->toArray();
    }
}