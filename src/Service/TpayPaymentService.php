<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\TpayPayerDTO;
use App\Entity\User;
use App\Enum\TpayResult;
use App\Manager\PaymentManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
    ): array {
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

        $response = $this->tpayApi->transactions()->createTransaction([
            'amount' => $amount,
            'description' => $description,
            'payer' => $payerData,
            'lang' => 'en',
        ]);

        if (TpayResult::tryFrom($response['result']) === TpayResult::SUCCESS) {
            $this->paymentManager->createPayment(
                $user,
                $amount,
                $description,
                $response['transactionId']
            );
    
            return $response;
        }

        throw new BadRequestHttpException(sprintf(
            'Tpay transaction failed: %s - %s',
            $response['errors'][0]['fieldName'],
            $response['errors'][0]['errorMessage']
        ));
    }

    public function getTpayApi(): TpayApi
    {
        return $this->tpayApi;
    }
}
