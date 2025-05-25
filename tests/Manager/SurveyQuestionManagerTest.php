<?php

use App\Manager\SurveyQuestionManager;
use App\Manager\SurveyOfferedAnswerManager;
use App\DataTransferObject\SurveyQuestionDTO;
use App\DataTransferObject\SurveyOfferedAnswerDTO;
use App\Entity\SurveyQuestion;
use App\Entity\SurveySection;
use App\Enum\QuestionType;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SurveyQuestionManagerTest extends TestCase
{
    public function testCreateQuestion(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $offeredAnswerManager = $this->createMock(SurveyOfferedAnswerManager::class);
        $manager = new SurveyQuestionManager($offeredAnswerManager, $entityManager);

        $dto = new SurveyQuestionDTO();
        $dto->setQuestion('Updated question?');
        $dto->setType(QuestionType::SINGLE_CHOICE->value);
        $dto->setOfferedAnswers([
            new SurveyOfferedAnswerDTO('Red'),
            new SurveyOfferedAnswerDTO('Blue')
        ]);

        $section = new SurveySection();

        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $offeredAnswerManager->expects($this->exactly(2))
            ->method('createOfferedAnswer');

        $question = $manager->createQuestion($dto, $section);

        $this->assertEquals($dto->getQuestion(), $question->getQuestion());
        $this->assertEquals(QuestionType::SINGLE_CHOICE, $question->getQuestionType());
        $this->assertSame($section, $question->getSection());
    }

    public function testEditQuestion(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $offeredAnswerManager = $this->createMock(\App\Manager\SurveyOfferedAnswerManager::class);

        $manager = new SurveyQuestionManager($offeredAnswerManager, $entityManager);

        $question = new SurveyQuestion();
        $dto = new SurveyQuestionDTO();
        $dto->setQuestion('Updated question?');
        $dto->setType(QuestionType::SINGLE_CHOICE->value);

        $entityManager->expects($this->once())->method('persist')->with($question);
        $entityManager->expects($this->once())->method('flush');

        $manager->editQuestion($question, $dto);

        $this->assertEquals('Updated question?', $question->getQuestion());
    }
}
