<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Password extends Constraint
{
    public string $messageTooShort = 'Password must be at least {{ limit }} characters long.';
    public string $messageCharacters = 'Password must contain at least one uppercase and one lowercase letter.';
    public int $minimumLength = 6;
}
