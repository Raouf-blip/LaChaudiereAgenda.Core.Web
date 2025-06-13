<?php

namespace Chaudiere\core\domain\entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasUuids;
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'password', 'role', 'nom', 'prenom'
    ];

    protected $hidden = [
        'password'
    ];

    // Constantes pour les rôles
    public const ROLE_USER = 100;
    public const ROLE_ADMIN = 50;
    public const ROLE_SUPER_ADMIN = 1;

    /**
     * Vérifie si l'utilisateur est un super-admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Vérifie si l'utilisateur est un admin ou super-admin
     */
    public function isAdmin(): bool
    {
        return $this->role >= self::ROLE_ADMIN;
    }
}