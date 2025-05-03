<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Payment;
use App\Entity\User;
use App\Enum\PaymentStatus;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;

class PaymentManager extends AbstractManager
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager);
    }

    public function createPayment(
        User $user,
        int $amount,
        string $externalTransactionId,
        string $paymentLink,
    ): Payment {
        $payment = new Payment();
        $payment->setExternalId($externalTransactionId)
            ->setAmount($amount)
            ->setStatus(PaymentStatus::PENDING)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUser($user)
            ->setPaymentLink($paymentLink)
        ;

        $this->saveEntity($payment);

        return $payment;
    }

    public function findPendingPaymentForUser(User $user): ?Payment
    {
        return $this->paymentRepository->findOneBy([
            'user' => $user,
            'status' => PaymentStatus::PENDING,
        ]);
    }

    public function findPaymentByExternalId(string $externalId): ?Payment
    {
        return $this->paymentRepository->findOneBy([
            'externalId' => $externalId
        ]);
    }
}
