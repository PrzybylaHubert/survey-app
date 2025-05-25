<?php

use App\Manager\SurveySectionManager;
use App\Manager\SurveyQuestionManager;
use App\DataTransferObject\SurveySectionDTO;
use App\DataTransferObject\SurveyQuestionDTO;
use App\Entity\Survey;
use App\Entity\SurveySection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SurveySectionManagerTest extends TestCase
{
    public function testCreateSection(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $questionManager = $this->createMock(SurveyQuestionManager::class);
        $manager = new SurveySectionManager($questionManager, $entityManager);

        $dto = new SurveySectionDTO();
        $dto->setName('Updated name');
        $dto->setQuestions([
            new SurveyQuestionDTO('What is your age?', 'single_choice', [])
        ]);

        $survey = new Survey();

        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $questionManager->expects($this->once())->method('createQuestion');

        $section = $manager->createSection($dto, $survey);

        $this->assertEquals($dto->getName(), $section->getName());
        $this->assertSame($survey, $section->getSurvey());
    }

    public function testEditSection(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $questionManager = $this->createMock(SurveyQuestionManager::class);

        $manager = new SurveySectionManager($questionManager, $entityManager);

        $section = new SurveySection();
        $dto = new SurveySectionDTO();
        $dto->setName('Updated name');

        $entityManager->expects($this->once())->method('persist')->with($section);
        $entityManager->expects($this->once())->method('flush');

        $manager->editSection($section, $dto);

        $this->assertEquals('Updated name', $section->getName());
    }
}
