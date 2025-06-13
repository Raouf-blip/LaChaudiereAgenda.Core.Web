<?php
declare(strict_types=1);

namespace Chaudiere\core\providers;

use Chaudiere\core\domain\entities\User;
use Chaudiere\core\domain\repositories\UserRepositoryInterface;
use Chaudiere\core\UseCase\AuthnService;

class SessionAuthProvider implements AuthProviderInterface
{
    private UserRepositoryInterface $userRepository;

    private const SESSION_USER_ID_KEY = 'user_id';
    private const SESSION_LAST_ACTIVITY_KEY = 'auth_last_activity';
    private const SESSION_AUTHENTICATED_KEY = 'authenticated';
    private const SESSION_LOGIN_TIME_KEY = 'login_time';
    private const SESSION_TIMEOUT = 1800;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        // S'assurer que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function canManageUsers(): bool
    {
        $user = $this->getSignedInUser();
        return $user !== null && $user->isSuperAdmin();
    }

    /**
     * Récupère l'utilisateur connecté (utilisée dans Twig)
     */
    public function getSignedInUser(): ?User
    {
        // Vérifier si la session est valide
        if (!$this->isSessionValid()) {
            return null;
        }

        // Récupérer l'ID utilisateur depuis la session (même clé que setActiveUserId)
        $userId = $_SESSION[self::SESSION_USER_ID_KEY] ?? null;

        if ($userId === null) {
            return null;
        }

        try {
            // Charger l'utilisateur depuis le repository
            $user = $this->userRepository->findById($userId);

            if ($user !== null) {
                // Mettre à jour le timestamp de dernière activité
                $this->updateLastActivity();
            }

            return $user;

        } catch (\Exception $e) {
            // En cas d'erreur, nettoyer la session
            $this->clearSession();
            return null;
        }
    }

    /**
     * Obtient le rôle de l'utilisateur connecté
     */
    public function getUserRole(): ?int
    {
        $user = $this->getSignedInUser();
        return $user ? $user->role : null;
    }

    /**
     * Vérifie si l'utilisateur connecté a le rôle admin
     */
    public function isAdmin(): bool
    {
        $user = $this->getSignedInUser();
        return $user && $user->role === AuthnService::ROLE_ADMIN;
    }

    /**
     * Vérifie si l'utilisateur connecté est un utilisateur standard
     */
    public function isUser(): bool
    {
        $user = $this->getSignedInUser();
        return $user && $user->role === AuthnService::ROLE_USER;
    }

    /**
     * Définit l'utilisateur actif (appelée par AuthnService::verifyCredentials)
     */
    public function setActiveUserId(string $userId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Régénérer l'ID de session pour la sécurité
        session_regenerate_id(true);


        $_SESSION[self::SESSION_USER_ID_KEY] = $userId;
        $_SESSION[self::SESSION_AUTHENTICATED_KEY] = true;
        $_SESSION[self::SESSION_LOGIN_TIME_KEY] = time();
        $_SESSION[self::SESSION_LAST_ACTIVITY_KEY] = time();

        // Optionnel : stocker le rôle pour éviter des requêtes
        try {
            $user = $this->userRepository->findById($userId);
            if ($user) {
                $_SESSION['auth_user_role'] = $user->role;
            }
        } catch (\Exception $e) {
            // Ignorer l'erreur, le rôle sera récupéré plus tard
        }
    }

    /**
     * Récupère l'ID de l'utilisateur connecté
     */
    public function getCurrentUserId(): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION[self::SESSION_USER_ID_KEY] ?? null;
    }

    /**
     * Vérifie si l'utilisateur est authentifié (utilisée par le middleware)
     */
    public function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier l'authentification ET la validité de la session
        return isset($_SESSION[self::SESSION_USER_ID_KEY]) &&
            !empty($_SESSION[self::SESSION_USER_ID_KEY]) &&
            ($_SESSION[self::SESSION_AUTHENTICATED_KEY] ?? false) &&
            $this->isSessionValid();
    }

    /**
     * Récupère l'utilisateur connecté (alias pour compatibilité)
     */
    public function getCurrentUser(): ?User
    {
        return $this->getSignedInUser();
    }

    /**
     * Déconnecte l'utilisateur (méthode principale)
     */
    public function clearActiveUser(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->clearSession();
    }

    /**
     * Méthode de convenance pour connecter un utilisateur avec l'objet User
     */
    public function login($user): void
    {
        $this->setActiveUserId((string)$user->id);
    }

    /**
     * Nettoie les données d'authentification de la session
     */
    private function clearSession(): void
    {
        // ✨ Nettoyer TOUTES les clés d'authentification
        unset($_SESSION[self::SESSION_USER_ID_KEY]);
        unset($_SESSION[self::SESSION_LAST_ACTIVITY_KEY]);
        unset($_SESSION[self::SESSION_AUTHENTICATED_KEY]);
        unset($_SESSION[self::SESSION_LOGIN_TIME_KEY]);
        unset($_SESSION['auth_user_role']);
    }

    /**
     * Vérifie si la session est valide (non expirée)
     */
    private function isSessionValid(): bool
    {
        $lastActivity = $_SESSION[self::SESSION_LAST_ACTIVITY_KEY] ?? null;

        if ($lastActivity === null) {
            return false;
        }

        // Vérifier si la session n'a pas expiré
        if ((time() - $lastActivity) > self::SESSION_TIMEOUT) {
            $this->clearSession();
            return false;
        }

        return true;
    }

    /**
     * Met à jour le timestamp de dernière activité
     */
    private function updateLastActivity(): void
    {
        $_SESSION[self::SESSION_LAST_ACTIVITY_KEY] = time();
    }
}