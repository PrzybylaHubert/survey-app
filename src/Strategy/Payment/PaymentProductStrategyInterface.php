<?php

declare(strict_types=1);

namespace App\Strategy\Payment;

use App\Entity\Payment;

interface PaymentProductStrategyInterface
{
    public function handleSuccessfulPayment(Payment $payment): void;

    public function handleReturnedPayment(Payment $payment): void;

    public function handleAlreadyPaidPayment(Payment $payment): void;

    public function handleUnsuccessfulPayment(Payment $payment): void;
}