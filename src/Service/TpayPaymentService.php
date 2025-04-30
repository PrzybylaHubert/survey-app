<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
        private readonly ParameterBagInterface $parameterBag,
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

    public function getTpayApi(): TpayApi
    {
        return $this->tpayApi;
    }
}
