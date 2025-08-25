<?php

namespace App;

enum OrderStatus:string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_map(fn($c) => $c->value, self::cases());
    }
}
