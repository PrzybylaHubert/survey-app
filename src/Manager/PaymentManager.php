<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Payment;
use App\Entity\User;
use App\Enum\PaymentStatus;

class PaymentManager extends AbstractManager
{
    public function createPayment(
        User $user,
        int $amount,
        string $externalTransactionId,
    ): Payment {
        $payment = new Payment();
        $payment->setExternalId($externalTransactionId)
            ->setAmount($amount)
            ->setStatus(PaymentStatus::PENDING)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUser($user);

        $this->saveEntity($payment);

        return $payment;
    }
}
