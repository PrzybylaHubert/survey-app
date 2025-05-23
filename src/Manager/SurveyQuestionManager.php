<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataTransferObject\SurveyQuestionDTO;
use App\Entity\SurveySection;
use App\Entity\SurveyQuestion;
use App\Enum\QuestionType;
use Doctrine\ORM\EntityManagerInterface;

class SurveyQuestionManager extends AbstractManager
{
    public function __construct(
        private readonly SurveyOfferedAnswerManager $surveyOfferedAnswerManager,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager);
    }

    public function createQuestion(
        SurveyQuestionDTO $questionData,
        SurveySection $surveySection,
        bool $flush = true,
    ): SurveyQuestion {
        $question = new SurveyQuestion();
        $question->setQuestion($questionData->getQuestion());
        $question->setQuestionType(QuestionType::from($questionData->getType()));
        $question->setSection($surveySection);

        foreach ($questionData->getOfferedAnswers() as $offeredAnswerData) {
            $this->surveyOfferedAnswerManager->createOfferedAnswer($offeredAnswerData, $question, false);
        }

        $this->saveEntity($question, $flush);

        return $question;
    }

    public function editQuestion(SurveyQuestion $question, SurveyQuestionDTO $questionData): void
    {
        $question->setQuestion($questionData->getQuestion());

        $this->saveEntity($question);
    }
}
