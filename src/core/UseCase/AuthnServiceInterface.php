<?php
declare(strict_types=1);

namespace Chaudiere\core\UseCase;

use Chaudiere\core\domain\entities\User;

interface AuthnServiceInterface
{
    public function register(string $email, string $password): User;

    public function verifyCredentials(string $email, string $password): ?User;
}
