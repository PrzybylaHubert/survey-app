<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\SurveyQuestionDTO;
use App\Entity\SurveyQuestion;
use App\Entity\SurveySection;
use App\Manager\SurveyManager;
use App\Manager\SurveyQuestionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
final class SurveyQuestionController extends AbstractController
{
    #[Route('/survey-question/{SurveySection}', name: 'survey_question_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(acceptFormat: 'json', validationGroups: ['create'])] SurveyQuestionDTO $surveyQuestionData,
        SurveyQuestionManager $surveyQuestionManager,
        SurveySection $surveySection,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('EDIT', $surveySection->getSurvey());

        $surveyQuestionManager->createQuestion($surveyQuestionData, $surveySection);

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/survey-question/{surveyQuestion}', name: 'survey_question_edit', methods: ['PATCH'])]
    public function edit(
        #[MapRequestPayload(acceptFormat: 'json')] SurveyQuestionDTO $surveyQuestionData,
        SurveyQuestion $surveyQuestion,
        SurveyQuestionManager $surveyQuestionManager,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('EDIT', $surveyQuestion->getSection()->getSurvey());

        $surveyQuestionManager->editQuestion($surveyQuestion, $surveyQuestionData);

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/survey-question/{surveyQuestion}', name: 'survey_question_delete', methods: ['DELETE'])]
    public function delete(
        surveyQuestion $surveyQuestion,
        SurveyManager $surveyManager,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('EDIT', $surveyQuestion->getSection()->getSurvey());

        $surveyManager->removeEntity($surveyQuestion);

        return $this->json([
            'success' => true,
        ]);
    }
}
