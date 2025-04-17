<?php

declare(strict_types=1);

namespace App\Enum;

enum QuestionType: string
{
    case SINGLE_CHOICE = 'single_choice';
    case MULTIPLE_CHOICE = 'multiple_choice';
    case TEXT = 'text';
    case NUMBER = 'number';
    case SELECT = 'select';
    case MULTIPLE_SELECT = 'multiple_select';

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
