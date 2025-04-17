<?php

declare(strict_types=1);

namespace App\Validator;

use App\DataTransferObject\SurveyAnswerInputDTO;
use App\Entity\Survey;
use App\Enum\QuestionType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SurveyAnswersValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SurveyAnswers) {
            throw new UnexpectedTypeException($constraint, SurveyAnswers::class);
        }

        if (!is_array($value->getAnswers())) {
            return;
        }

        $survey = $constraint->survey;

        if (!$survey instanceof Survey) {
            throw new \LogicException('Survey must be passed to the validator context.');
        }

        $questionMap = [];
        foreach ($survey->getSurveySections() as $section) {
            foreach ($section->getQuestions() as $question) {
                $questionMap[$question->getId()] = $question;
            }
        }

        /** @var SurveyAnswerInputDTO $answerInputDTO */
        foreach ($value->getAnswers() as $questionId => $answerInputDTO) {
            if (!isset($questionMap[$questionId])) {
                $this->context->buildViolation('Invalid question ID.')
                    ->atPath("answers[$questionId].questionId")
                    ->addViolation();
                continue;
            }

            $question = $questionMap[$questionId];
            $limiter = $question->getLimiter();

            $offeredAnswerIds = array_map(
                fn($oa) => $oa->getId(),
                $question->getOfferedAnswers()->toArray()
            );

            if ($question->getQuestionType()->value !== $answerInputDTO->getQuestionType()) {
                $this->context->buildViolation('Invalid question type.')
                    ->atPath("answers[$questionId].answer")
                    ->addViolation();
                    continue;
            }
            $answer = $answerInputDTO->getAnswer();
            switch ($question->getQuestionType()) {
                case QuestionType::NUMBER:
                    if (!is_numeric($answer)) {
                        $this->context->buildViolation('Answer must be a number for numeric questions.')
                            ->atPath("answers[$questionId].answer")
                            ->addViolation();
                    }
                    if (!is_null($limiter) && is_numeric($answer) && abs($answer) > $limiter) {
                        $this->context->buildViolation('Answer exceeds max value.')
                            ->atPath("answers[$questionId].answer")
                            ->addViolation();
                    }
                    break;
                case QuestionType::TEXT:
                    if (!is_string($answer)) {
                        $this->context->buildViolation('Answer must be a string for open questions.')
                            ->atPath("answers[$questionId].answer")
                            ->addViolation();
                    }
                    if (!is_null($limiter) && strlen($answer) > $limiter) {
                        $this->context->buildViolation('Answer exceeds the character limit.')
                            ->atPath("answers[$questionId].answer")
                            ->addViolation();
                    }
                    break;
                case QuestionType::SINGLE_CHOICE:
                case QuestionType::SELECT:
                    if (!is_numeric($answer)) {
                        $this->context->buildViolation('Answer must be one offered answer ID.')
                            ->atPath("answers[$questionId].answer")
                            ->addViolation();
                    } elseif (!in_array((int)$answer, $offeredAnswerIds, true)) {
                        $this->context->buildViolation('Invalid offered answer selected.')
                            ->atPath("answers[$questionId].answer")
                            ->addViolation();
                    }
                    break;
                case QuestionType::MULTIPLE_CHOICE:
                case QuestionType::MULTIPLE_SELECT:
                    if (!is_array($answer) || empty($answer)) {
                        $this->context->buildViolation('Must select at least one answer.')
                            ->atPath("answers[$questionId].answer")
                            ->addViolation();
                    } else {
                        foreach ($answer as $aid) {
                            if (!in_array((int)$aid, $offeredAnswerIds, true)) {
                                $this->context->buildViolation("Invalid answer ID: $aid")
                                    ->atPath("answers[$questionId].answer")
                                    ->addViolation();
                            }
                        }
                        if ($limiter !== null && count($answer) > $limiter) {
                            $this->context->buildViolation('Too many answers selected.')
                                ->atPath("answers[$questionId].answer")
                                ->addViolation();
                        }
                    }
                    break;
            }
        }
    }
}
