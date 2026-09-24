<?php

namespace App\Enums;

enum RefundRequestStatus: string
{

    case PENDING = 'pending';
    case PROCESSED = 'processed';
}
