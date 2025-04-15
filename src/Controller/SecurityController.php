<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\RegisterDTO;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class SecurityController extends AbstractController
{
    public function __construct()
    {
       
    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return $this->json([
            'message' => 'Login successful',
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['POST '])]
    public function logout(): JsonResponse
    {
        return $this->json([
            'message' => 'Login successful',
        ]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        UserManager $userManager,
        #[MapRequestPayload(acceptFormat: 'json')] RegisterDTO $userData,
    ): JsonResponse {
        if ($userManager->getRepository()->findOneBy(['email' => $userData->getEmail()])) {
            return $this->json(['error' => 'User already exists'], 400);
        }

        $userManager->registerUser($userData->getEmail(), $userData->getPassword());

        return $this->json(['message' => 'User registered successfully'], 201);
    }
}
