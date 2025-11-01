<?php

namespace App\Controller;

use App\Dto\ForgetPasswordDto;
use App\Dto\RegisterUserDto;
use App\Dto\ResetPasswordDto;
use App\Entity\PasswordResetToken;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private MailService $mailService,
        private EntityManagerInterface $em){}


    #[Route('/api/login', name: 'app_login')]
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        return new JsonResponse([ 'username' => $user->getUserIdentifier(), 'roles' => $user->getRoles() ]);
    }

    #[Route('api/register', name:'app_register')]
    public function register(
    #[MapRequestPayload] RegisterUserDto $userDTO,
    UserFactory $userFactory): JsonResponse
    {

        $user = $userFactory->createFromDto($userDTO);

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ]
        ], 201);
    }

    #[Route('api/forget_password', name:'app_forget_password')]
    public function forgetPassword(#[MapRequestPayload] ForgetPasswordDto $forgetPasswordDto)
    {
        $email = $forgetPasswordDto->email;

        $user= $this->userRepository->findOneBy(['email'=>$email]);

        if(!$user){
            return $this->json(['message'=>'If this email exists, a reset link has been sent']);
        }

        $token=bin2hex(random_bytes(32));
        $resetToken= new PasswordResetToken();
        $resetToken->setOwner($user);
        $resetToken->setToken($token);

        $this->em->persist($resetToken);
        $this->em->flush();

        $content='this is your token to reset your password : '.$token;

        $this->mailService->sendEmail('test@example.com','forget password',$content);
        
        return $this->json(['message' => 'If this email exists, a reset link has been sent']);

    }

    #[Route('api/reset_password',name: 'app_reset_password')]
    public function resetPassword(#[MapRequestPayload] ResetPasswordDto $resetPasswordDto, UserPasswordHasherInterface $passwordHasher)
    {

        $tokenValue=$resetPasswordDto->token;
        $newPassword=$resetPasswordDto->newPassword;

        $token=$this->em->getRepository(PasswordResetToken::class)->findOneBy(['token'=>$tokenValue]);

        if(!$token || $token->isExpired()){
            return $this->json(['error' => 'Invalid or expired token'], 400);
        }

        $user= $token->getOwner();
        $user->setPassword($passwordHasher->hashPassword($user,$newPassword));

        $this->em->remove($token);
        $this->em->flush();

        return $this->json(['message' => 'Password has been reset successfully']);

    }
}
