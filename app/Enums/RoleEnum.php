<?php

namespace App\Enums;

enum RoleEnum: string
{

    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
    case SUPPORT = 'support';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
