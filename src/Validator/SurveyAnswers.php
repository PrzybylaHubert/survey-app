<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\Survey;
use Symfony\Component\Validator\Constraint;

class SurveyAnswers extends Constraint
{
    public string $message = 'Invalid survey answers.';

    public function __construct(
        public ?Survey $survey = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }
}
