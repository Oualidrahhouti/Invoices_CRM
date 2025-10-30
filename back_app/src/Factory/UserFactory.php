<?php 
namespace App\Factory;

use App\Dto\RegisterUserDto;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function createFromDto(RegisterUserDto $dto): User
    {
        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $dto->password)
        );

        return $user;
    }
}
