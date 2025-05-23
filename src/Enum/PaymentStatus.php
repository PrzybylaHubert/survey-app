<?php

declare(strict_types=1);

namespace App\Enum;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case CORRECT = 'correct';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case REFUND = 'refund';
    case CANCELED = 'canceled';
}
