<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

class SurveySectionDTO
{
    #[Assert\Length(min: 3, max: 255)]
    #[Assert\NotBlank]
    private string $name;

    /**
     * @var SurveyQuestionDTO[]
     */
    #[Assert\Valid]
    #[Assert\Type('array')]
    #[Assert\NotNull(message: "Questions must be provided.")]
    #[Assert\Count(min: 1, minMessage: "You must provide at least one question.")]
    private array $questions;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return SurveyQuestionDTO[]
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    /**
     * @param SurveyQuestionDTO[] $questions
     */
    public function setQuestions(array $questions): void
    {
        $this->questions = $questions;
    }
}
