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
    #[Route('/survey/{survey}', name: 'get_survey')]
    public function show(Survey $survey): JsonResponse
    {
        return $this->json(
            data: [
                'survey' => $survey,
            ],
            context: ['groups' => ['surveyInfo']],
        );
    }

    #[Route('/survey', name: 'survey', methods: ['POST'])]
    public function create(
        #[CurrentUser()] User $user,
        #[MapRequestPayload(acceptFormat: 'json')] SurveyDTO $surveyData,
        SurveyManager $surveyManager,
    ): JsonResponse {
        $surveyManager->createSurvey($surveyData, $user);

        return $this->json([
            'success' => true,
        ]);
    }
}
