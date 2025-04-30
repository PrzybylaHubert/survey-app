<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TpayPaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
final class PaymentController extends AbstractController
{
    #[Route('/paymentTest', name: 'payment_test', methods: ['GET'])]
    public function findSurveys(TpayPaymentService $tpayPaymentService): JsonResponse {
        $response = $tpayPaymentService->getTpayApi()->Transactions->getTransactions();
        print_r($response);
        return $this->json(
            data: [
                'test' => 'test',
            ],
        );
    }
}
