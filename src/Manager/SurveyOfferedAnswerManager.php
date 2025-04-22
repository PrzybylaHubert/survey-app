<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataTransferObject\SurveyOfferedAnswerDTO;
use App\Entity\SurveyQuestion;
use App\Entity\SurveyOfferedAnswer;
use Doctrine\ORM\EntityManagerInterface;

class SurveyOfferedAnswerManager extends AbstractManager
{
    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager);
    }

    public function createOfferedAnswer(
        SurveyOfferedAnswerDTO $offeredAnswerData,
        SurveyQuestion $surveyQuestion,
        bool $flush = true,
    ): SurveyOfferedAnswer {
        $answer = new SurveyOfferedAnswer();
        $answer->setOfferedAnswer($offeredAnswerData->getAnswer());
        $answer->setQuestion($surveyQuestion);

        $this->saveEntity($answer, $flush);

        return $answer;
    }

    public function editOfferedAnswer(SurveyOfferedAnswer $offeredAnswer, SurveyOfferedAnswerDTO $offeredAnswerData): void
    {
        $offeredAnswer->setOfferedAnswer($offeredAnswerData->getAnswer());

        $this->saveEntity($offeredAnswer);
    }
}
