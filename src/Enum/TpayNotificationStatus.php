<?php

declare(strict_types=1);

namespace App\Enum;

enum TpayNotificationStatus: string
{
    case FALSE = 'FALSE';
    case TRUE = 'TRUE';
    case PAID = 'PAID';
    case CHARGEBACK = 'CHARGEBACK';
}
