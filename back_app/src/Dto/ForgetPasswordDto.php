<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ForgetPasswordDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email is required')]
        #[Assert\Email(message: 'Please provide a valid email address')]
        public string $email
    ) {}
}