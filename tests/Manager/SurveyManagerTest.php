<?php

namespace App\Tests\Manager;

use App\DataTransferObject\SurveyDTO;
use App\Entity\Survey;
use App\Entity\User;
use App\Manager\SurveyManager;
use App\Manager\SurveySectionManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class SurveyManagerTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private SurveySectionManager $surveySectionManager;
    private MessageBusInterface $messageBus;
    private SurveyManager $surveyManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->surveySectionManager = $this->createMock(SurveySectionManager::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);

        $this->surveyManager = new SurveyManager(
            $this->messageBus,
            $this->surveySectionManager,
            $this->entityManager
        );
    }

    public function testCreateSurvey(): void
    {
        $user = new User();

        $sectionDto1 = $this->createMock(\App\DataTransferObject\SurveySectionDTO::class);
        $sectionDto2 = $this->createMock(\App\DataTransferObject\SurveySectionDTO::class);

        $surveyDto = new SurveyDTO();

        $surveyDto->setName('Survey name');
        $surveyDto->setDescription('Survey description');
        $surveyDto->setIsActive(true);
        $surveyDto->setSections([$sectionDto1, $sectionDto2]);

        $this->entityManager->expects($this->once())->method('persist')->with($this->isInstanceOf(Survey::class));
        $this->entityManager->expects($this->once())->method('flush');

        $this->surveySectionManager
            ->expects($this->exactly(2))
            ->method('createSection')
            ->withConsecutive(
                [$sectionDto1, $this->isInstanceOf(Survey::class), false],
                [$sectionDto2, $this->isInstanceOf(Survey::class), false]
            );

        $this->messageBus->method('dispatch')
            ->willReturnCallback(function ($message) {
                return new Envelope($message);
            });

        $survey = $this->surveyManager->createSurvey($surveyDto, $user);
        $this->assertInstanceOf(Survey::class, $survey);
        $this->assertSame($user, $survey->getAuthor());
        $this->assertSame('Survey name', $survey->getName());
        $this->assertSame('Survey description', $survey->getDescription());
        $this->assertTrue($survey->isActive());
    }

    public function testEditSurvey(): void
    {
        $survey = new Survey();

        $surveyDto = $this->createMock(SurveyDTO::class);
        $surveyDto->method('getName')->willReturn('Updated name');
        $surveyDto->method('getDescription')->willReturn('Updated description');
        $surveyDto->method('isActive')->willReturn(false);

        // Expect save
        $this->entityManager->expects($this->once())->method('persist')->with($survey);
        $this->entityManager->expects($this->once())->method('flush');

        $this->surveyManager->editSurvey($survey, $surveyDto);

        $this->assertSame('Updated name', $survey->getName());
        $this->assertSame('Updated description', $survey->getDescription());
        $this->assertFalse($survey->isActive());
    }
}
