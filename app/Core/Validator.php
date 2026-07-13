<?php

namespace App\Core;

abstract class Validator
{
    public static function required(string $value): bool
    {
        return trim($value) !== '';
    }

    public static function nonNegativeNumber(string $value): bool
    {
        return is_numeric($value) && (float) $value >= 0;
    }
}
