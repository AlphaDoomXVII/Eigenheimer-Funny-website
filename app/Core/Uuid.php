<?php

namespace App\Core;

class Uuid
{
    public static function generate(?string $data = null): string
    {
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Versie 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Bits 6-7 naar 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
