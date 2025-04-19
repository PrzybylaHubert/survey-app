<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager extends AbstractManager
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function registerUser(string $email, string $plainPassword): void
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $this->saveEntity($user);
    }
}
