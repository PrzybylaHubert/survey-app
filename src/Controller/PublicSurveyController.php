<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\SubmitSurveyDTO;
use App\Entity\Survey;
use App\Entity\SurveyAssignment;
use App\Entity\User;
use App\Service\SurveySearchService;
use App\Service\SurveyWorkflowService;
use App\Validator\SurveyAnswers;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_')]
final class PublicSurveyController extends AbstractController
{
    #[Route('/search-surveys', name: 'search_surveys', methods: ['GET'])]
    public function findSurveys(
        Request $request,
        SurveySearchService $surveySearchService,
    ): JsonResponse {
        $query = $request->query->get('query', '');
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        $results = $surveySearchService->search($query, $page, $limit);

        return $this->json(
            data: [
                'surveys' => $results,
                'pagination' => [
                    'page' => $page,
                    'perPage' => 10,
                    'total' => $results->getNbResults(),
                    'totalPages' => ceil($results->getNbResults() / 10),
                ],
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

        $surveyAssignment = $surveyWorkflowService->startSurvey($survey, $user);

        return $this->json(
            data: [
                'survey' => $survey,
                '$surveyAssignment' => $surveyAssignment,
            ],
            context: ['groups' => ['surveyInfo', 'surveyFull', 'assignmentInfo']],
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
}
