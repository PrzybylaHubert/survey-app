<?php

declare(strict_types=1);

namespace App\DataTransferObject;

class SurveyAnswerInputDTO
{
    private string $questionType;

    /** @var int|string|array<int> */
    private int|string|array $answer;

    public function getQuestionType(): string
    {
        return $this->questionType;
    }
    public function setQuestionType(string $questionType): void
    {
        $this->questionType = $questionType;
    }

    /**
     * @return int|string|array<int>
     */
    public function getAnswer(): int|string|array
    {
        return $this->answer;
    }

    /**
     * @param int|string|array<int> $answer
     */
    public function setAnswer(int|string|array $answer): void
    {
        $this->answer = $answer;
    }
}
