<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\SubmitSurveyDTO;
use App\DataTransferObject\SurveyAnswerInputDTO;
use App\Entity\Survey;
use App\Entity\SurveyAssignment;
use App\Entity\SurveyOfferedAnswer;
use App\Entity\SurveyQuestion;
use App\Entity\SurveyUserAnswer;
use App\Entity\User;
use App\Enum\QuestionType;
use App\Repository\SurveyAssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SurveyWorkflowService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SurveyAssignmentRepository $assignmentRepository,
    ) {
    }

    public function startSurvey(Survey $survey, User $user): SurveyAssignment
    {
        $existingAssignment = $this->assignmentRepository->findOneBy([
            'survey' => $survey,
            'user' => $user,
            'dateFinish' => null
        ]);

        if ($existingAssignment !== null) {
            throw new AccessDeniedException('You already have an ongoing survey.');
        }

        $surveyAssignment = new SurveyAssignment();
        $surveyAssignment->setSurvey($survey);
        $surveyAssignment->setUser($user);

        $this->entityManager->persist($surveyAssignment);
        $this->entityManager->flush();

        return $surveyAssignment;
    }

    public function submitSurvey(
        SurveyAssignment $surveyAssignment,
        SubmitSurveyDTO $submittedSurveyDTO
    ): void {
        /** @var SurveyAnswerInputDTO $answerInputDTO */
        foreach ($submittedSurveyDTO->getAnswers() as $questionId => $answerInputDTO) {
            switch (QuestionType::from($answerInputDTO->getQuestionType())) {
                case QuestionType::NUMBER:
                case QuestionType::TEXT:
                    $this->handleOpenInput(
                        $surveyAssignment,
                        $questionId,
                        (string)$answerInputDTO->getAnswer()
                    );
                    break;
                case QuestionType::SINGLE_CHOICE:
                case QuestionType::SELECT:
                    $this->handleChoiceInput(
                        $surveyAssignment,
                        $questionId,
                        (int)$answerInputDTO->getAnswer()
                    );
                    break;
                case QuestionType::MULTIPLE_CHOICE:
                case QuestionType::MULTIPLE_SELECT:
                    foreach ($answerInputDTO->getAnswer() as $answerId) {
                        $this->handleChoiceInput(
                            $surveyAssignment,
                            $questionId,
                            (int)$answerId
                        );
                    }
                    break;
            }
        }
        $surveyAssignment->setDateFinish(new \DateTimeImmutable());

        $this->entityManager->flush();
    }

    private function handleOpenInput(
        SurveyAssignment $surveyAssignment,
        int $questionId,
        string $answerInput
    ): void {
        $answer = new SurveyUserAnswer();
        $answer->setSurveyAssignment($surveyAssignment);
        $answer->setQuestion($this->entityManager->getReference(SurveyQuestion::class, $questionId));
        $answer->setValue($answerInput);

        $this->entityManager->persist($answer);
    }

    private function handleChoiceInput(
        SurveyAssignment $surveyAssignment,
        int $questionId,
        int $answerId
    ): void {
            $answer = new SurveyUserAnswer();
        $answer->setSurveyAssignment($surveyAssignment);
        $answer->setQuestion($this->entityManager->getReference(SurveyQuestion::class, $questionId));
        $answer->setOfferedAnswer($this->entityManager->getReference(SurveyOfferedAnswer::class, $answerId));

        $this->entityManager->persist($answer);
    }
}
