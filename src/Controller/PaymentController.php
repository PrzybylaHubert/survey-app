<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\TpayPayerDTO;
use App\Entity\User;
use App\Service\TpayPaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_')]
final class PaymentController extends AbstractController
{
    #[Route('/get-payment', name: 'get_payment', methods: ['POST'])]
    public function testPayment(
        #[MapRequestPayload(acceptFormat: 'json')] TpayPayerDTO $tpayPayerData,
        #[CurrentUser()] User $user,
        TpayPaymentService $tpayPaymentService,
    ): JsonResponse {
        $response = $tpayPaymentService->createTransaction(
            user: $user,
            amount: 100,
            description: 'Premium user payment',
            tpayPayerData: $tpayPayerData,
        );

        return $this->json(
            data: [
                'success' => true,
                'paymentUrl' => $response['transactionPaymentUrl'],
            ],
        );
    }
}
