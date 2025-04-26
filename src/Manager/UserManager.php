<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager extends AbstractManager
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager);
    }

    public function registerUser(string $email, string $plainPassword): void
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $this->saveEntity($user);
    }

    public function validatePassword(User $user, string $plainPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }

    public function changePassword(User $user, string $newPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $this->saveEntity($user);
    }
}
