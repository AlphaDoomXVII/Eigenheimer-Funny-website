<?php

namespace App\Modules\Kamers\Models;

use App\Core\Model;

class KamerModel extends Model
{
    protected static string $table = 'kamers';
    protected static array $fillable = ['UUID', 'name', 'description', 'price', 'photo_path', 'is_available'];

    public static function all(string $orderBy = 'id DESC'): array
    {
        try {
            return parent::all($orderBy);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function available(): array
    {
        try {
            $stmt = \App\Core\Database::pdo()->query('SELECT * FROM kamers WHERE is_available = 1 ORDER BY id DESC');
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function toggleAvailability(int $id): void
    {
        $kamer = static::find($id);
        if ($kamer === null) {
            return;
        }

        static::update($id, ['is_available' => $kamer['is_available'] ? 0 : 1]);
    }
}
