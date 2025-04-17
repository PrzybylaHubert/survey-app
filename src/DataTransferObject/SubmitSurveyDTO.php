<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

class SubmitSurveyDTO
{
    /**
     * @var array<int, SurveyAnswerInputDTO> $answers
     */
    #[Assert\NotBlank]
    public array $answers = [];

    /**
     * @return array<int, SurveyAnswerInputDTO>
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    /**
     * @param array<int, SurveyAnswerInputDTO> $answers
     */
    public function setAnswers(array $answers): void
    {
        $this->answers = $answers;
    }
}