<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataTransferObject\SurveySectionDTO;
use App\Entity\Survey;
use App\Entity\SurveySection;

class SurveySectionManager extends AbstractManager
{
    public function __construct(
        private readonly SurveyQuestionManager $surveyQuestionManager,
    ) {
    }

    public function createSection(
        SurveySectionDTO $sectionData,
        Survey $survey,
        bool $flush = true,
    ): SurveySection {
        $section = new SurveySection();
        $section->setName($sectionData->getName());
        $section->setSurvey($survey);
    
        foreach ($sectionData->getQuestions() as $questionData) {
            $this->surveyQuestionManager->createQuestion($questionData, $section, false);
        }

        $this->saveEntity($section, $flush);

        return $section;
    }

    public function editSection(SurveySection $section, SurveySectionDTO $sectionData): void
    {
        $section->setName($sectionData->getName());

        $this->saveEntity($section);
    }
}
