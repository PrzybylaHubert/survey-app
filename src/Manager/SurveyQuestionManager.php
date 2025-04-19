<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataTransferObject\SurveyQuestionDTO;
use App\Entity\SurveySection;
use App\Entity\SurveyQuestion;
use App\Enum\QuestionType;

class SurveyQuestionManager extends AbstractManager
{
    public function __construct(
        private readonly SurveyOfferedAnswerManager $surveyOfferedAnswerManager,
    ) {
    }

    public function createSurveyQuestion(
        SurveyQuestionDTO $questionData,
        SurveySection $surveySection,
        bool $flush = true,
    ): SurveyQuestion {
        $question = new SurveyQuestion();
        $question->setQuestion($questionData->getQuestion());
        $question->setQuestionType(QuestionType::from($questionData->getType()));
        $question->setSection($surveySection);

        foreach ($questionData->getOfferedAnswers() as $offeredAnswerData) {
            $this->surveyOfferedAnswerManager->createSurveyOfferedAnswer($offeredAnswerData, $question, false);
        }

        $this->saveEntity($question, $flush);

        return $question;
    }

    public function editSUrveyQuestion(SurveyQuestion $question, SurveyQuestionDTO $questionData): void
    {
        $question->setQuestion($questionData->getQuestion());
        $question->setQuestionType(QuestionType::from($questionData->getType()));

        $this->saveEntity($question);
    }
}
