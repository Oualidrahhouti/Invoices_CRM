<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ResetPasswordDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Token is required')]
        public string $token,
        
        #[Assert\NotBlank(message: 'New password is required')]
        #[Assert\Length(
            min: 8,
            minMessage: 'Password must be at least {{ limit }} characters long'
        )]
        public string $newPassword
    ) {}
}