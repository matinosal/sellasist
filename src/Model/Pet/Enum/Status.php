<?php

namespace App\Model\Pet\Enum;

Enum Status: string
{
    case AVAILABLE = 'available';
    case PENDING = 'pending';
    case SOLD = 'sold';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}