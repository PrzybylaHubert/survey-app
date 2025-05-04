<?php

declare(strict_types=1);

namespace App\Strategy\Payment;

use App\Entity\Payment;
use App\Enum\PaymentStatus;
use App\Manager\PaymentManager;
use Psr\Log\LoggerInterface;

class PremiumUserPaymentStrategy implements PaymentProductStrategyInterface
{
    public function __construct(
        private readonly PaymentManager $paymentManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function handleSuccessfulPayment(Payment $payment): void
    {
        $user = $payment->getUser();
        $user->setIsPremium(true);

        $payment->setStatus(PaymentStatus::SUCCESS);
        $payment->setPaidAt(new \DateTimeImmutable());

        $this->paymentManager->saveChanges();
    }

    public function handleReturnedPayment(Payment $payment): void
    {
        $user = $payment->getUser();
        $user->setIsPremium(false);

        $payment->setStatus(PaymentStatus::REFUND);
        $payment->setRefundedAt(new \DateTimeImmutable());

        $this->paymentManager->saveChanges();
    }

    public function handleAlreadyPaidPayment(Payment $payment): void
    {
        $this->logger->notice(sprintf(
            'Handling pending payment with tpay already paid status. PaymentId: %d',
            $payment->getId()
        ));

        $this->handleSuccessfulPayment($payment);
    }

    public function handleUnsuccessfulPayment(Payment $payment): void
    {
        $this->logger->warning(sprintf(
            'UnsuccesfulPayment. PaymentId: %d',
            $payment->getId()
        ));
    }
}
