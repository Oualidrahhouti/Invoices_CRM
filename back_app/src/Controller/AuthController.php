<?php

namespace App\Controller;

use App\Dto\RegisterUserDto;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'app_login')]
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        return new JsonResponse([ 'username' => $user->getUserIdentifier(), 'roles' => $user->getRoles() ]);
    }

    #[Route('api/register', name:'app_register')]
    public function register(Request $request,
    EntityManagerInterface $em,
    ValidatorInterface $validator,
    UserFactory $userFactory): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        $userDTO = new RegisterUserDto();
        $userDTO->email=$data['email'] ?? '';
        $userDTO->password=$data['password'] ?? '';

        $errors= $validator->validate($userDTO);
        if(count($errors)>0){
            return $this->json(['errors' => (string) $errors], 400);
        }

        $user = $userFactory->createFromDto($userDTO);

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ]
        ], 201);
    }
}
