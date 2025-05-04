<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\TpayPayerDTO;
use App\Entity\Payment;
use App\Entity\User;
use App\Manager\PaymentManager;
use App\Service\TpayPaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_')]
final class PaymentController extends AbstractController
{
    #[Route('/create-payment', name: 'create_payment', methods: ['POST'])]
    public function createPayment(
        #[MapRequestPayload(acceptFormat: 'json')] TpayPayerDTO $tpayPayerData,
        #[CurrentUser()] User $user,
        TpayPaymentService $tpayPaymentService,
    ): JsonResponse {
        if ($user->isPremium()) {
            return $this->json(
                data: [
                    'success' => false,
                    'message' => 'User is already premium user'
                ],
            );
        }

        $paymentUrl = $tpayPaymentService->createTransaction(
            user: $user,
            amount: 100,
            description: 'Premium user payment',
            tpayPayerData: $tpayPayerData,
        );

        return $this->json(
            data: [
                'success' => true,
                'paymentUrl' => $paymentUrl,
            ],
        );
    }

    #[Route('/payments', name: 'get_user_payments', methods: ['GET'])]
    public function getUserPayments(
        #[CurrentUser()] User $user,
        PaymentManager $paymentManager,
    ): JsonResponse {
        $userPayments = $paymentManager->getUserPayments($user);

        return $this->json(
            data: [
                'userPayments' => $userPayments,
            ],
            context: ['groups' => ['paymentData']],
        );
    }

    #[Route('/check-payment/{payment}', name: 'manual_check_payment', methods: ['POST'])]
    public function manualCheckPayment(
        #[CurrentUser()] User $user,
        Payment $payment,
        TpayPaymentService $tpayPaymentService,
    ): JsonResponse {
        if ($payment->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $externalPayment = $tpayPaymentService->getTpayApi()->transactions()->getTransactionById(
            $payment->getExternalTransactionId()
        );

        $tpayPaymentService->handleManualCheck($payment, $externalPayment['status']);

        return $this->json(
            data: [
                'payment' => $payment,
                'externalPayment' => $externalPayment
            ],
            context: ['groups' => ['paymentData']],
        );
    }

    #[Route('/await-notification', name: 'await_notification', methods: ['POST'])]
    public function awaitNotification(
        Request $request,
        TpayPaymentService $tpayPaymentService,
        PaymentManager $paymentManager,
    ): Response {
        $transactionId = $request->request->get('tr_id');
        $status = $request->request->get('tr_status');
    
        if (!$transactionId || !$status) {
            return new Response('FALSE - Missing data', Response::HTTP_BAD_REQUEST);
        }

        $payment = $paymentManager->findPaymentByExternalId($transactionId);
    
        if (!$payment) {
            return new Response('FALSE - Payment not found', Response::HTTP_NOT_FOUND);
        }

        $tpayPaymentService->handleNotification($payment, $status);
    
        return new Response('TRUE', Response::HTTP_OK);
    }
}
