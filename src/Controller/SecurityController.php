<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\ChangePasswordDTO;
use App\DataTransferObject\RegisterDTO;
use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

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
        UserRepository $userRepository,
        #[MapRequestPayload(acceptFormat: 'json')] RegisterDTO $userData,
    ): JsonResponse {
        if ($userRepository->findOneBy(['email' => $userData->getEmail()])) {
            return $this->json(['error' => 'User already exists'], 400);
        }

        $userManager->registerUser($userData->getEmail(), $userData->getPassword());

        return $this->json(['message' => 'User registered successfully'], 201);
    }

    #[Route('/change-password', name: 'change_password', methods: ['PATCH'])]
    public function changePassword(
        #[MapRequestPayload(acceptFormat: 'json')] ChangePasswordDTO $passwordData,
        #[CurrentUser()] User $user,
        UserManager $userManager,
    ): JsonResponse {
        if (!$userManager->validatePassword($user, $passwordData->getCurrentPassword())) {
            return $this->json(['error' => 'Current password is incorrect.'], 400);
        }

        if ($passwordData->getCurrentPassword() === $passwordData->getNewPassword()) {
            return $this->json(['error' => 'New password must be different from the current password.'], 400);
        }

        $userManager->changePassword($user, $passwordData->getNewPassword());

        return $this->json(['message' => 'Password changed successfully.']);
    }
}
