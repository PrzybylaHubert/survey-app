<?php

namespace App\Tests\Manager;

use App\Manager\AbstractManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class AbstractManagerTest extends TestCase
{
    private AbstractManager $abstractManager;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->abstractManager = $this->getMockForAbstractClass(AbstractManager::class, [$this->entityManager]);
    }


    public function testSaveEntityWithFlush(): void
    {
        $entity = new \stdClass();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($entity);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->abstractManager->saveEntity($entity, true);
    }

    public function testSaveEntityWithoutFlush(): void
    {
        $entity = new \stdClass();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($entity);

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->abstractManager->saveEntity($entity, false);
    }

    public function testRemoveEntityWithFlush(): void
    {
        $entity = new \stdClass();

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($entity);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->abstractManager->removeEntity($entity, true);
    }

    public function testRemoveEntityWithoutFlush(): void
    {
        $entity = new \stdClass();

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($entity);

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->abstractManager->removeEntity($entity, false);
    }

    public function testSaveChanges(): void
    {
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->abstractManager->saveChanges();
    }
}
