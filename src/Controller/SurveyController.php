<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\SurveyDTO;
use App\Entity\Survey;
use App\Entity\User;
use App\Manager\SurveyManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_')]
final class SurveyController extends AbstractController
{
    #[Route('/survey', name: 'survey_create', methods: ['POST'])]
    public function create(
        #[CurrentUser()] User $user,
        #[MapRequestPayload(acceptFormat: 'json', validationGroups: ['create'])] SurveyDTO $surveyData,
        SurveyManager $surveyManager,
    ): JsonResponse {
        $surveyManager->createSurvey($surveyData, $user);

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/survey/{survey}', name: 'survey_edit', methods: ['PATCH'])]
    public function edit(
        #[MapRequestPayload(acceptFormat: 'json')] SurveyDTO $surveyData,
        Survey $survey,
        SurveyManager $surveyManager,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('EDIT', $survey);

        $surveyManager->editSurvey($survey, $surveyData);

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/survey/{survey}', name: 'survey_delete', methods: ['DELETE'])]
    public function delete(
        Survey $survey,
        SurveyManager $surveyManager,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('EDIT', $survey);

        $surveyManager->removeEntity($survey);

        return $this->json([
            'success' => true,
        ]);
    }
}
