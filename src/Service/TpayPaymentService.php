<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\TpayPayerDTO;
use App\Entity\Payment;
use App\Entity\User;
use App\Enum\PaymentStatus;
use App\Enum\TpayNotificationStatus;
use App\Enum\TpayResult;
use App\Manager\PaymentManager;
use App\Strategy\Payment\PaymentStrategySelector;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Tpay\OpenApi\Api\TpayApi;
use Tpay\OpenApi\Utilities\Logger;

class TpayPaymentService
{
    private const TPAY_LOG_PATH = '/var/log/tpay/';

    private TpayApi $tpayApi;

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly PaymentManager $paymentManager,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly PaymentStrategySelector $strategySelector,
        string $projectDir,
        string $tpayClientId,
        string $tpayClientSecret,
    ) {
        Logger::setLogPath($projectDir . self::TPAY_LOG_PATH);
        $this->tpayApi = new TpayApi($tpayClientId, $tpayClientSecret);

        $token = $this->cache->get('tpay_token', function (ItemInterface $item): string {
            $item->expiresAfter(7190);

            $this->tpayApi->authorization();
        
            return serialize($this->tpayApi->getToken());
        });

        $this->tpayApi->setCustomToken(unserialize($token));
    }

    public function createTransaction(
        User $user,
        int $amount,
        string $description,
        TpayPayerDTO $tpayPayerData,
    ): string {
        $pendingPayment = $this->paymentManager->findPendingPaymentForUser($user);

        if ($pendingPayment !== null) {
            $externalTransactionId = $pendingPayment->getExternalTransactionId();
            try {
                // error, if not found throws 500
                $transaction = $this->tpayApi->transactions()->getTransactionById($externalTransactionId);
                // enum provided by tpay would be great
                $paymentStatus = PaymentStatus::tryFrom($transaction['status']);

                if ($paymentStatus === PaymentStatus::PENDING) {
                    return $pendingPayment->getPaymentLink();
                } else {
                    $pendingPayment->setStatus($paymentStatus);
                    $this->paymentManager->saveEntity($pendingPayment);
                }
            } catch (\Exception $e) {
            }

        }

        $payerData = array_filter([
            'email' => $user->getEmail(),
            'name' => $tpayPayerData->getName(),
            'phone' => $tpayPayerData->getPhone(),
            'address' => $tpayPayerData->getAddress(),
            'code' => $tpayPayerData->getCode(),
            'city' => $tpayPayerData->getCity(),
            'country' => $tpayPayerData->getCountry(),
            'taxId' => $tpayPayerData->getTaxId(),
        ], fn($value) => !is_null($value));

        $transaction = $this->tpayApi->transactions()->createTransaction([
            'amount' => $amount,
            'description' => $description,
            'payer' => $payerData,
            'lang' => 'en',
            'callbacks' => [
                'notification' => [
                  'url' => $this->urlGenerator->generate('api_await_notification', [], UrlGeneratorInterface::ABSOLUTE_URL)
                ]
            ]
        ]);

        // transaction title as transaction id?
        if (TpayResult::tryFrom($transaction['result']) === TpayResult::SUCCESS) {
            $this->paymentManager->createPayment(
                $user,
                $amount,
                $transaction['title'],
                $transaction['transactionId'],
                $transaction['transactionPaymentUrl'],
            );

            return $transaction['transactionPaymentUrl'];
        }

        throw new BadRequestHttpException(sprintf(
            'Tpay transaction failed: %s - %s',
            $transaction['errors'][0]['fieldName'],
            $transaction['errors'][0]['errorMessage']
        ));
    }

    public function handleManualCheck(Payment $payment, string $status): void
    {
        $strategy = $this->strategySelector->select($payment);

        match (PaymentStatus::tryFrom($status)) {
            PaymentStatus::CORRECT => $payment->isPending() ? $strategy->handleSuccessfulPayment($payment) : null,
            PaymentStatus::REFUND => $payment->isPaid() ? $strategy->handleReturnedPayment($payment) : null,
            default => throw new \InvalidArgumentException("Unknown status: $status"),
        };
    }

    public function handleNotification(Payment $payment, string $status): void
    {
        $strategy = $this->strategySelector->select($payment);

        match (TpayNotificationStatus::tryFrom($status)) {
            TpayNotificationStatus::FALSE => $strategy->handleUnsuccessfulPayment($payment),
            TpayNotificationStatus::TRUE => $payment->isPending() ? $strategy->handleSuccessfulPayment($payment) : null,
            TpayNotificationStatus::PAID => $payment->isPending() ? $strategy->handleAlreadyPaidPayment($payment) : null,
            TpayNotificationStatus::CHARGEBACK => $payment->isPaid() ? $strategy->handleReturnedPayment($payment) : null,
            default => throw new \InvalidArgumentException("Unknown status: $status"),
        };
    }

    public function getTpayApi(): TpayApi
    {
        return $this->tpayApi;
    }
}
