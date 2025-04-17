<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

class SurveyOfferedAnswerDTO
{
    #[Assert\Length(max: 511)]
    #[Assert\NotBlank]
    private string $answer;

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }
}
