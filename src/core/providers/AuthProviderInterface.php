<?php
declare(strict_types=1);

namespace Chaudiere\core\providers;


use Chaudiere\core\domain\entities\User;

interface AuthProviderInterface
{
    public function getSignedInUser(): ?User;


    public function setActiveUserId(string $userId): void;


    public function clearActiveUser(): void;

    public function isAuthenticated(): bool;

    public function getCurrentUserId(): ?string;

    public function getUserRole(): ?int;

    public function isAdmin(): bool;

    public function isUser(): bool;
}