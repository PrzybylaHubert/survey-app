<?php

declare(strict_types=1);

namespace App\Strategy\Payment;

use App\Entity\Payment;

class PaymentStrategySelector
{
    public function __construct(
        private PremiumUserPaymentStrategy $premiumUserStrategy,
        // Add more strategies here in the future
    ) {}

    public function select(Payment $payment): PaymentProductStrategyInterface
    {
        // no other strategies right now.
        return match (1) {
            1 => $this->premiumUserStrategy,
            default => throw new \InvalidArgumentException('No strategy exist.'),
        };
    }
}
