<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Enum\QuestionType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SurveyQuestionDTO
{
    #[Assert\Length(min: 3, max: 511)]
    #[Assert\NotBlank]
    private string $question;

    #[Assert\Choice(
        callback: [QuestionType::class, 'values'],
        message: 'The value you selected is not valid. Available choices are: {{ choices }}.'
    )]
    #[Assert\NotBlank(groups: ['create'])]
    private string $type;

    /**
     * @var SurveyOfferedAnswerDTO[]
     */
    #[Assert\Valid(groups: ['create'])]
    #[Assert\Type('array', groups: ['create'])]
    private array $offeredAnswers = [];

    #[Assert\Callback(groups: ['create'])]
    public function validateOfferedAnswers(ExecutionContextInterface $context): void
    {
        if (in_array($this->type, [QuestionType::TEXT->value, QuestionType::NUMBER->value], true)) {
            if (count($this->offeredAnswers) > 0) {
                $context->buildViolation('Offered answers should be null for this question type.')
                        ->atPath('offeredAnswers')
                        ->addViolation();
            }
        } elseif (is_null($this->offeredAnswers) || count($this->offeredAnswers) < 2) {
                $context->buildViolation('You must provide at least 2 offered answers.')
                        ->atPath('offeredAnswers')
                        ->addViolation();
        }
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return SurveyOfferedAnswerDTO[]
     */
    public function getOfferedAnswers(): array
    {
        return $this->offeredAnswers;
    }

    /**
     * @param SurveyOfferedAnswerDTO[] $offeredAnswers
     */
    public function setOfferedAnswers(array $offeredAnswers): void
    {
        $this->offeredAnswers = $offeredAnswers;
    }
}
