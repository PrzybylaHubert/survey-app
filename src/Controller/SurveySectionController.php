<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\SurveySectionDTO;
use App\Entity\Survey;
use App\Entity\SurveySection;
use App\Manager\SurveyManager;
use App\Manager\SurveySectionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
final class SurveySectionController extends AbstractController
{
    #[Route('/survey-section/{survey}', name: 'survey_section_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(acceptFormat: 'json', validationGroups: ['create'])] SurveySectionDTO $surveySectionData,
        SurveySectionManager $surveySectionManager,
        Survey $survey,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('EDIT', $survey);

        $surveySectionManager->createSection($surveySectionData, $survey);

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/survey-section/{surveySection}', name: 'survey_section_edit', methods: ['PATCH'])]
    public function edit(
        #[MapRequestPayload(acceptFormat: 'json')] SurveySectionDTO $surveySectionData,
        SurveySection $surveySection,
        SurveySectionManager $surveySectionManager,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('EDIT', $surveySection->getSurvey());

        $surveySectionManager->editSection($surveySection, $surveySectionData);

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/survey-section/{surveySection}', name: 'survey_section_delete', methods: ['DELETE'])]
    public function delete(
        SurveySection $surveySection,
        SurveyManager $surveyManager,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('EDIT', $surveySection->getSurvey());

        $surveyManager->removeEntity($surveySection);

        return $this->json([
            'success' => true,
        ]);
    }
}
