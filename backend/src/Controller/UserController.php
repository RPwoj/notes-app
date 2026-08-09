<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Dto\UserPostDto;
use App\Repository\UserRepository;
use App\Service\UserService;

final class UserController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private UserService $userService,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/user', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $dto = new UserPostDto();
        $dto->password = $requestData['password'] ?? null;
        $dto->email = $requestData['email'] ?? null;

        $errors = $this->validator->validate($dto);
        $errorsArray = [];
        
        if (count($errors) > 0) {
            foreach($errors as $error) {
                $errorsArray[] = $error->getMessage();
            }

            return $this->json(['errors' => $errorsArray], 400);
        }
    
        $data = $this->userService->createUser($dto);

        return new JsonResponse($data);
    }

    #[Route('/login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        // $requestData = json_decode($request->getContent(), true);
        // $email = $requestData['email'] ?? null;
        // $password = $requestData['password'] ?? null;

        // if (!$email || !$password) {
        //     return $this->json([
        //         'error' => 'Email and password are required.',
        //     ], 400);
        // }

        // $user = $this->userRepository->findOneBy(['email' => $email]);

        // if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
        //     return $this->json([
        //         'error' => 'Invalid credentials.',
        //     ], 401);
        // }

        // return $this->json([
        //     'message' => 'Login successful.',
        //     'userId' => $user->getId(),
        // ]);
    }
}
