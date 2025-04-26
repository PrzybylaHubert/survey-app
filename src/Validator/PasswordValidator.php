<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Password) {
            throw new UnexpectedTypeException($constraint, Password::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (strlen($value) < $constraint->minimumLength) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ limit }}', $constraint->minimumLength)
                ->addViolation();
            return;
        }

        if (!preg_match('/[a-z]/', $value) || !preg_match('/[A-Z]/', $value)) {
            $this->context->buildViolation($constraint->messageCharacters)
                ->addViolation();
        }
    }
}
