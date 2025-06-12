<?php

namespace Chaudiere\core\UseCase;
use Chaudiere\core\domain\entities\User;
use Chaudiere\core\domain\repositories\UserRepositoryInterface;
use Chaudiere\core\providers\AuthProviderInterface;

class AuthnService implements AuthnServiceInterface
{
    private UserRepositoryInterface $userRepository;
    private AuthProviderInterface $authProvider;

    public const ROLE_USER = 100;
    public const ROLE_ADMIN = 50;
    public const ROLE_SUPER_ADMIN = 1;

    public function __construct(UserRepositoryInterface $userRepository, AuthProviderInterface $authProvider)
    {
        $this->userRepository = $userRepository;
        $this->authProvider = $authProvider;
    }

    public function register(string $email, string $password): User
    {
        // Validation des données d'entrée
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format.");
        }
        $this->validatePassword($password);

        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser !== null) {
            throw new \RuntimeException("Il y a déjà un compte existant avec cet email");
        }

        $hashedPassword = $this->hashPassword($password);

        $user = new User();
        $user->email = $email;
        $user->password_hash = $hashedPassword;
        $user->role = self::ROLE_USER;

        $this->userRepository->save($user);
        return $user;
    }

    public function registerUser(string $userId, string $password): User
    {
        $this->validatePassword($password);

        $existingUser = $this->userRepository->findByUserId($userId);
        if ($existingUser !== null) {
            throw new \RuntimeException("Un utilisateur avec cet ID utilisateur existe déjà");
        }

        $hashedPassword = $this->hashPassword($password);

        $user = new User();
        $user->id = base64_encode(random_bytes(16));
        $user->email = $userId;
        $user->password_hash = $hashedPassword;
        $user->role = self::ROLE_USER;

        $this->userRepository->save($user);
        return $user;
    }

    /**
     * Fonctionnalité 13: Création d'utilisateurs par le super-admin
     * Seul un super-admin peut créer de nouveaux administrateurs
     */
    public function createAdminUser(string $email, string $password, int $role = self::ROLE_ADMIN): User
    {
        // Vérifier que l'utilisateur actuel est un super-admin
        $currentUser = $this->getCurrentUser();
        if ($currentUser === null || !$currentUser->isSuperAdmin()) {
            throw new \RuntimeException("Seul un super-administrateur peut créer des utilisateurs administrateurs");
        }

        // Validation des données
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Format d'email invalide");
        }

        $this->validatePassword($password);

        // Vérifier que le rôle est valide (ne peut pas créer un super-admin)
        if (!in_array($role, [self::ROLE_USER, self::ROLE_ADMIN])) {
            throw new \InvalidArgumentException("Rôle invalide");
        }

        // Vérifier que l'utilisateur n'existe pas déjà
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser !== null) {
            throw new \RuntimeException("Un utilisateur avec cet email existe déjà");
        }

        $hashedPassword = $this->hashPassword($password);

        $user = new User();
        $user->email = $email;
        $user->password_hash = $hashedPassword;
        $user->role = $role;

        $this->userRepository->save($user);
        return $user;
    }

    /**
     * Récupère l'utilisateur actuellement connecté
     */
    public function getCurrentUser(): ?User
    {
        $userId = $this->getCurrentUserId();
        if ($userId === null) {
            return null;
        }

        return $this->userRepository->findById($userId);
    }

    /**
     * Vérifie si l'utilisateur actuel peut gérer les utilisateurs
     */
    public function canManageUsers(): bool
    {
        $currentUser = $this->getCurrentUser();
        return $currentUser !== null && $currentUser->isSuperAdmin();
    }

    public function verifyCredentials(string $email, string $password): ?User
    {
        // Validation basique
        if (empty($email) || empty($password)) {
            return null;
        }

        // Récupérer l'utilisateur par email
        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            return null;
        }

        // Vérifier le mot de passe
        if ($this->verifyPassword($password, $user->password_hash)) {
            if (!isset($user->id)) {
                throw new \LogicException("User object is missing an ID property.");
            }
            $this->authProvider->setActiveUserId((string)$user->id);
            return $user;
        }

        return null;
    }

    public function signOut(): void
    {
        $this->authProvider->clearActiveUser();
    }

    public function isSignedIn(): bool
    {
        return $this->authProvider->isAuthenticated();
    }

    public function getCurrentUserId(): ?string
    {
        return $this->authProvider->getCurrentUserId();
    }

    /**
     * Valide le mot de passe
     */
    private function validatePassword(string $password): void
    {
        if (empty($password)) {
            throw new \InvalidArgumentException("Password cannot be empty.");
        }

        if (strlen($password) < 6) {
            throw new \InvalidArgumentException("Password must be at least 6 characters long.");
        }

        if (strlen($password) > 255) {
            throw new \InvalidArgumentException("Password is too long (max 255 characters).");
        }
    }

    /**
     * Hache un mot de passe
     */
    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => 12,
        ]);
    }

    /**
     * Vérifie un mot de passe contre son hash
     */
    private function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}