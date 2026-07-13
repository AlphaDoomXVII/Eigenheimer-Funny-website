<?php

namespace App\Modules\Bestellen\Models;

use App\Core\Model;

class MenuItemModel extends Model
{
    protected static string $table = 'order_food';
    protected static array $fillable = ['name', 'price', 'UUID'];

    public static function all(string $orderBy = 'id DESC'): array
    {
        try {
            return parent::all($orderBy);
        } catch (\Throwable $e) {
            return [];
        }
    }
}
