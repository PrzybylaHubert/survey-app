<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataTransferObject\SurveyDTO;
use App\Entity\Survey;
use App\Entity\User;
use App\Message\NewSurveyCreatedMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class SurveyManager extends AbstractManager
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly SurveySectionManager $surveySectionManager,
    ) {
    }

    public function createSurvey(
        SurveyDTO $surveyData,
        User $user,
        bool $flush = true,
    ): Survey {
        $survey = new Survey();
        $survey->setName($surveyData->getName());
        $survey->setDescription($surveyData->getDescription());
        $survey->setAuthor($user);
        $survey->setIsActive($surveyData->isActive());

        foreach ($surveyData->getSections() as $sectionData) {
            $this->surveySectionManager->createSection($sectionData, $survey, false);
        }

        $this->saveEntity($survey, $flush);

        $this->messageBus->dispatch(new NewSurveyCreatedMessage(
            $survey->getName(),
            $survey->getDescription()
        ));

        return $survey;
    }

    public function editSurvey(Survey $survey, SurveyDTO $surveyData): void
    {
        $survey->setName($surveyData->getName());
        $survey->setDescription($surveyData->getDescription());
        $survey->setIsActive($surveyData->isActive());

        $this->saveEntity($survey);
    }
}
