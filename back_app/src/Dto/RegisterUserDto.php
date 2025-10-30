<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
#[UniqueEntity(fields: ['email'], entityClass: User::class, message:'email already exist !')]
class RegisterUserDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;
}