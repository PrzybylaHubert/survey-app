<?php

declare(strict_types=1);

namespace App\Enum;

enum TpayResult: string
{
    case SUCCESS = 'success';
    case FAILED = 'failed';
}
