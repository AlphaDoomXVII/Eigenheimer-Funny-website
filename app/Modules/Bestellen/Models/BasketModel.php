<?php

namespace App\Modules\Bestellen\Models;

use App\Core\Uuid;

/**
 * Sessie-gebaseerd winkelmandje. Geen databasetabel, dus geen App\Core\Model-subklasse.
 */
class BasketModel
{
    public static function items(): array
    {
        return $_SESSION['items'] ?? [];
    }

    public static function add(string $price, string $uuid, string $name, string $dagdeel = ''): array
    {
        $item = [
            'price_item' => $price,
            'uuid_item' => $uuid,
            'name_item' => $name,
            'dagdeel_item' => $dagdeel,
            'basket_item_uuid' => Uuid::generate(),
        ];

        $_SESSION['items'][] = $item;

        return $_SESSION['items'];
    }

    public static function remove(string $basketItemUuid): void
    {
        if (!isset($_SESSION['items'])) {
            return;
        }

        $index = array_search($basketItemUuid, array_column($_SESSION['items'], 'basket_item_uuid'));

        if ($index !== false) {
            unset($_SESSION['items'][$index]);
        }
    }

    public static function clear(): void
    {
        unset($_SESSION['items']);
    }
}
