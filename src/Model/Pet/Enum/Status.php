<?php

namespace App\Model\Pet\Enum;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Status",
    type: "string",
    enum: ["available", "pending", "sold"]
)]
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