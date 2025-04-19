<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\SurveyOfferedAnswerDTO;
use App\Entity\SurveyOfferedAnswer;
use App\Entity\SurveyQuestion;
use App\Manager\SurveyOfferedAnswerManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
final class SurveyOfferedAnswerController extends AbstractController
{
    #[Route('/survey-offered-answer/{surveyQuestion}', name: 'survey_offered_answer_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(acceptFormat: 'json', validationGroups: ['create'])] SurveyOfferedAnswerDTO $surveyOfferedAnswerData,
        SurveyOfferedAnswerManager $surveyOfferedAnswerManager,
        SurveyQuestion $surveyQuestion,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('EDIT', $surveyQuestion->getSection()->getSurvey());

        $surveyOfferedAnswerManager->createOfferedAnswer($surveyOfferedAnswerData, $surveyQuestion);

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/survey-offered-answer/{surveyOfferedAnswer}', name: 'survey_offered_answer_edit', methods: ['PATCH'])]
    public function edit(
        #[MapRequestPayload(acceptFormat: 'json')] SurveyOfferedAnswerDTO $surveyOfferedAnswerData,
        SurveyOfferedAnswer $surveyOfferedAnswer,
        SurveyOfferedAnswerManager $surveyOfferedAnswerManager,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('EDIT', $surveyOfferedAnswer->getQuestion()->getSection()->getSurvey());

        $surveyOfferedAnswerManager->editOfferedAnswer($surveyOfferedAnswer, $surveyOfferedAnswerData);

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/survey-offered-answer/{surveyOfferedAnswer}', name: 'survey_offered_answer_delete', methods: ['DELETE'])]
    public function delete(
        SurveyOfferedAnswer $surveyOfferedAnswer,
        SurveyOfferedAnswerManager $surveyOfferedAnswerManager,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('EDIT', $surveyOfferedAnswer->getQuestion()->getSection()->getSurvey());

        $surveyOfferedAnswerManager->removeEntity($surveyOfferedAnswer);

        return $this->json([
            'success' => true,
        ]);
    }
}
