<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\SubmitSurveyDTO;
use App\DataTransferObject\SurveyDTO;
use App\Entity\Survey;
use App\Entity\SurveyAssignment;
use App\Entity\User;
use App\Manager\SurveyManager;
use App\Service\SurveyWorkflowService;
use App\Validator\SurveyAnswers;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_')]
final class SurveyController extends AbstractController
{
    #[Route('/survey/{survey}', name: 'get_survey', methods: ['GET'])]
    public function show(Survey $survey): JsonResponse
    {
        $this->denyAccessUnlessGranted('VIEW', $survey);

        return $this->json(
            data: [
                'survey' => $survey,
            ],
            context: ['groups' => ['surveyInfo']],
        );
    }

    #[Route('/full-survey/{survey}', name: 'get_full_survey', methods: ['GET'])]
    public function showFull(
        #[MapEntity(expr: 'repository.findFullSurveyById(survey)')] Survey $survey,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('VIEW', $survey);

        return $this->json(
            data: [
                'survey' => $survey,
            ],
            context: ['groups' => ['surveyInfo', 'surveyFull']],
        );
    }

    #[Route('/start-survey/{survey}', name: 'start_survey', methods: ['POST'])]
    public function startSurvey(
        #[MapEntity(expr: 'repository.findFullSurveyById(survey)')] Survey $survey,
        #[CurrentUser()] User $user,
        SurveyWorkflowService $surveyWorkflowService,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('VIEW', $survey);

        $surveyWorkflowService->startSurvey($survey, $user);

        return $this->json(
            data: [
                'survey' => $survey,
            ],
            context: ['groups' => ['surveyInfo', 'surveyFull']],
        );
    }

    #[Route('/submit-survey/{survey}/{surveyAssignment}', name: 'submit_survey', methods: ['POST'])]
    public function submitSurvey(
        #[MapEntity(expr: 'repository.findFullSurveyById(survey)')] Survey $survey,
        SurveyAssignment $surveyAssignment,
        #[CurrentUser()] User $user,
        SurveyWorkflowService $surveyWorkflowService,
        SerializerInterface $serializer,
        Request $request,
        ValidatorInterface $validator,
    ): JsonResponse {
        if ($surveyAssignment->getUser() !== $user) {
            throw $this->createAccessDeniedException('You are not allowed to submit this survey.');
        }

        if ($surveyAssignment->getDateFinish() !== null) {
            throw $this->createAccessDeniedException('This survey has already been submitted.');
        }

        $submittedSurveyDTO = $serializer->deserialize(
            $request->getContent(),
            SubmitSurveyDTO::class,
            'json'
        );

        $errors = $validator->validate(
            $submittedSurveyDTO,
            new SurveyAnswers(survey: $survey)
        );

        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 422);
        }

        $surveyWorkflowService->submitSurvey($surveyAssignment, $submittedSurveyDTO);

        return $this->json([
            'success' => true,
        ]);
    }

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

        $surveyManager->deleteSurvey($survey);

        return $this->json([
            'success' => true,
        ]);
    }
}
