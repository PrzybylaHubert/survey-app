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
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): void
    {
        // handled in security.yaml
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
