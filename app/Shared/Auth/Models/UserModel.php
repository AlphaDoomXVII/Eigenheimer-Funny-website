<?php

namespace App\Shared\Auth\Models;

use App\Core\Database;
use App\Core\Model;

class UserModel extends Model
{
    protected static string $table = 'gebruikers';
    protected static array $fillable = ['UUID', 'naam', 'email', 'wachtwoord_hash', 'rol', 'is_actief'];

    public const ROLES = ['admin', 'medewerker', 'gast'];

    public static function findByEmail(string $email): ?array
    {
        try {
            $stmt = Database::pdo()->prepare('SELECT * FROM gebruikers WHERE email = ? AND is_actief = 1');
            $stmt->execute([$email]);
            $row = $stmt->fetch();
            return $row === false ? null : $row;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        try {
            return parent::all($orderBy);
        } catch (\Throwable $e) {
            return [];
        }
    }
}
