<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataTransferObject\SurveyDTO;
use App\Entity\Survey;
use App\Entity\SurveySection;
use App\Entity\SurveyQuestion;
use App\Entity\SurveyOfferedAnswer;
use App\Entity\User;
use App\Enum\QuestionType;
use Doctrine\ORM\EntityManagerInterface;

class SurveyManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createSurvey(SurveyDTO $surveyData, User $user): Survey
    {
        $survey = new Survey();
        $survey->setName($surveyData->getName());
        $survey->setDescription($surveyData->getDescription());
        $survey->setAuthor($user);
        $survey->setIsActive($surveyData->isActive());
        $this->entityManager->persist($survey);

        foreach ($surveyData->getSections() as $sectionData) {
            $section = new SurveySection();
            $section->setName($sectionData->getName());
            $section->setSurvey($survey);
            $this->entityManager->persist($section);

            foreach ($sectionData->getQuestions() as $questionData) {
                $question = new SurveyQuestion();
                $question->setQuestion($questionData->getQuestion());
                $question->setQuestionType(QuestionType::from($questionData->getType()));
                $question->setSection($section);
                $this->entityManager->persist($question);

                foreach ($questionData->getOfferedAnswers() as $answerData) {
                    $answer = new SurveyOfferedAnswer();
                    $answer->setOfferedAnswer($answerData->getAnswer());
                    $answer->setQuestion($question);
                    $this->entityManager->persist($answer);
                }
            }
        }

        
        $this->entityManager->flush();

        return $survey;
    }

    public function editSurvey(Survey $survey, SurveyDTO $surveyData): void
    {
        $survey->setName($surveyData->getName());
        $survey->setDescription($surveyData->getDescription());
        $survey->setIsActive($surveyData->isActive());

        $this->entityManager->flush();
    }

    public function deleteSurvey(Survey $survey): void
    {
        $this->entityManager->remove($survey);

        $this->entityManager->flush();
    }
}
