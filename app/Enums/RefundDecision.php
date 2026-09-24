<?php

namespace App\Enums;

enum RefundDecision: string
{

    case APPROVED = 'approved';
    case DENIED = 'denied';
    case ESCALATED = 'escalated';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
