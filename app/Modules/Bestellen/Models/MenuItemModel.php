<?php

namespace App\Modules\Bestellen\Models;

use App\Core\Database;
use App\Core\Model;

class MenuItemModel extends Model
{
    public const DAGDELEN = ['ontbijt', 'lunch', 'diner'];

    protected static string $table = 'order_food';
    protected static array $fillable = ['name', 'price', 'UUID', 'dagdeel', 'is_available'];

    public static function all(string $orderBy = 'id DESC'): array
    {
        try {
            return parent::all($orderBy);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function byDagdeel(string $dagdeel): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT * FROM order_food WHERE dagdeel = ? AND is_available = 1 ORDER BY id DESC'
            );
            $stmt->execute([$dagdeel]);
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function toggleAvailability(int $id): void
    {
        $item = static::find($id);
        if ($item === null) {
            return;
        }

        static::update($id, ['is_available' => $item['is_available'] ? 0 : 1]);
    }
}
