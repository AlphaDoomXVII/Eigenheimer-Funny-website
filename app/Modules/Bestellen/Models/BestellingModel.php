<?php

namespace App\Modules\Bestellen\Models;

use App\Core\Database;
use App\Core\Model;

class BestellingModel extends Model
{
    protected static string $table = 'bestellingen';
    protected static array $fillable = ['UUID', 'klant_naam', 'items', 'totaal', 'status'];

    public static function all(string $orderBy = 'id DESC'): array
    {
        try {
            return parent::all($orderBy);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function openstaand(): array
    {
        try {
            $stmt = Database::pdo()->query(
                "SELECT * FROM bestellingen WHERE status = 'openstaand' ORDER BY created_at ASC"
            );
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function afhandelen(int $id): void
    {
        static::update($id, ['status' => 'afgehandeld']);
    }
}
