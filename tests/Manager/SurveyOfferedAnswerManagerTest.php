<?php

namespace App\Tests\Manager;

use App\Manager\SurveyOfferedAnswerManager;
use App\DataTransferObject\SurveyOfferedAnswerDTO;
use App\Entity\SurveyOfferedAnswer;
use App\Entity\SurveyQuestion;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SurveyOfferedAnswerManagerTest extends TestCase
{
    public function testCreateOfferedAnswer(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $manager = new SurveyOfferedAnswerManager($entityManager);

        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $dto = new SurveyOfferedAnswerDTO();
        $dto->setAnswer('Answer text');
        $question = new SurveyQuestion();

        $answer = $manager->createOfferedAnswer($dto, $question);

        $this->assertEquals('Answer text', $answer->getOfferedAnswer());
        $this->assertSame($question, $answer->getQuestion());
    }

    public function testEditOfferedAnswer(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $manager = new SurveyOfferedAnswerManager($entityManager);

        $offeredAnswer = new SurveyOfferedAnswer();
        $dto = new SurveyOfferedAnswerDTO();
        $dto->setAnswer('Updated answer');

        $entityManager->expects($this->once())->method('persist')->with($offeredAnswer);
        $entityManager->expects($this->once())->method('flush');

        $manager->editOfferedAnswer($offeredAnswer, $dto);

        $this->assertEquals('Updated answer', $offeredAnswer->getOfferedAnswer());
    }
}
