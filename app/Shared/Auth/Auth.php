<?php

namespace App\Shared\Auth;

/**
 * Sessiegedreven auth-helper, zelfde stijl als App\Shared\Rechten\Models\FeatureModel::isEnabled():
 * controllers checken dit aan het begin van een actie en redirecten zelf naar /login.
 */
class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_naam'] = $user['naam'];
        $_SESSION['user_rol'] = $user['rol'];
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_naam'], $_SESSION['user_rol']);
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'naam' => $_SESSION['user_naam'],
            'rol' => $_SESSION['user_rol'],
        ];
    }

    public static function rol(): ?string
    {
        return $_SESSION['user_rol'] ?? null;
    }

    /** @param string|string[] $roles */
    public static function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return self::check() && in_array(self::rol(), $roles, true);
    }
}
