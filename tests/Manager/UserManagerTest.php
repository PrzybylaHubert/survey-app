<?php

namespace App\Tests\Manager;

use App\Entity\User;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManagerTest extends TestCase
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private UserManager $userManager;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->userManager = new UserManager($this->passwordHasher, $this->entityManager);
    }

    public function testRegisterUser(): void
    {
        $email = 'test@example.com';
        $plainPassword = 'secret';
        $hashedPassword = 'hashed_secret';

        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), $plainPassword)
            ->willReturn($hashedPassword);

        $this->entityManager->expects($this->once())->method('persist')->with($this->isInstanceOf(User::class));
        $this->entityManager->expects($this->once())->method('flush');

        $this->userManager->registerUser($email, $plainPassword);
    }

    public function testValidatePassword(): void
    {
        $user = new User();
        $plainPassword = 'testpass';

        $this->passwordHasher->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, $plainPassword)
            ->willReturn(true);

        $result = $this->userManager->validatePassword($user, $plainPassword);
        $this->assertTrue($result);
    }

    public function testChangePassword(): void
    {
        $user = new User();
        $newPassword = 'newpassword';
        $hashed = 'hashed_new_password';

        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, $newPassword)
            ->willReturn($hashed);

        $this->entityManager->expects($this->once())->method('persist')->with($user);
        $this->entityManager->expects($this->once())->method('flush');

        $this->userManager->changePassword($user, $newPassword);

        $this->assertEquals($hashed, $user->getPassword());
    }
}
