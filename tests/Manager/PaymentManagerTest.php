<?php

namespace App\Tests\Manager;

use App\Entity\Payment;
use App\Entity\User;
use App\Enum\PaymentStatus;
use App\Manager\PaymentManager;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PaymentManagerTest extends TestCase
{
    private PaymentRepository $paymentRepository;
    private EntityManagerInterface $entityManager;
    private PaymentManager $paymentManager;

    protected function setUp(): void
    {
        $this->paymentRepository = $this->createMock(PaymentRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->paymentManager = new PaymentManager(
            $this->paymentRepository,
            $this->entityManager
        );
    }

    public function testCreatePayment(): void
    {
        $user = new User();
        $amount = 1999;
        $externalId = 'EXT123';
        $transactionId = 'TXN456';
        $paymentLink = 'https://payment.link';

        $this->entityManager->expects($this->once())->method('persist')->with($this->isInstanceOf(Payment::class));
        $this->entityManager->expects($this->once())->method('flush');

        $payment = $this->paymentManager->createPayment($user, $amount, $externalId, $transactionId, $paymentLink);

        $this->assertInstanceOf(Payment::class, $payment);
    }

    public function testFindPendingPaymentForUser(): void
    {
        $user = new User();
        $expectedPayment = new Payment();

        $this->paymentRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['user' => $user, 'status' => PaymentStatus::PENDING])
            ->willReturn($expectedPayment);

        $result = $this->paymentManager->findPendingPaymentForUser($user);
        $this->assertSame($expectedPayment, $result);
    }

    public function testFindPaymentByExternalId(): void
    {
        $externalId = 'EXT123';
        $expectedPayment = new Payment();

        $this->paymentRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['externalId' => $externalId])
            ->willReturn($expectedPayment);

        $result = $this->paymentManager->findPaymentByExternalId($externalId);
        $this->assertSame($expectedPayment, $result);
    }
}
