<?php

namespace App\Shared\Rechten\Models;

use App\Core\Database;

/**
 * Aan/uit-schakelbare features (bv. 'bestellen', 'kamers'), zie database/schema.sql.
 * Ontbreekt een feature in de database, dan geldt die als standaard aan (fail-open) —
 * zo blokkeert een lege/nog-niet-gemigreerde tabel de site niet.
 */
class FeatureModel
{
    private static ?array $cache = null;

    public static function isEnabled(string $feature): bool
    {
        if (self::$cache === null) {
            self::$cache = self::load();
        }

        return self::$cache[$feature] ?? true;
    }

    /** @return array<string, bool> feature => enabled, voor het rechten-instellingenscherm */
    public static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = self::load();
        }

        return self::$cache;
    }

    public static function toggle(string $feature): void
    {
        try {
            Database::pdo()
                ->prepare('UPDATE features SET enabled = NOT enabled WHERE feature = ?')
                ->execute([$feature]);
        } catch (\Throwable $e) {
            // Fail-open: als de tabel ontbreekt, is er niets om te togglen.
        }

        self::$cache = null;
    }

    private static function load(): array
    {
        try {
            $rows = Database::pdo()->query('SELECT feature, enabled FROM features')->fetchAll();
        } catch (\Throwable $e) {
            return [];
        }

        $features = [];
        foreach ($rows as $row) {
            $features[$row['feature']] = (bool) $row['enabled'];
        }

        return $features;
    }
}
