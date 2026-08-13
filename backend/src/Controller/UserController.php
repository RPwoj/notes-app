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
use App\Service\EmailService;
use App\Entity\User as UserEntity;

final class UserController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private UserService $userService,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EmailService $emailService
    ) {
    }

    #[Route('/register', methods: ['POST'])]
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

        $this->emailService->sendEmail(
            $dto->email,
            'Confirm your account',
            'Please click the following link to confirm your account: <a href="http://notes.local/confirm?token=' . $data['confirmationToken'] . '">Confirm Account</a>'
        );

        return new JsonResponse($data); 
    }

    #[Route('/confirm', methods: ['GET'])]
    public function confirm(Request $request): JsonResponse
    {
        $token = $request->query->get('token');

        if (!$token) {
            return $this->json([
                'error' => 'Token is required.',
            ], 400);
        }

        $result = $this->userService->confirmUser($token);

        if ($result['success']) {
            return $this->json([
                'message' => 'User confirmed successfully.',
            ]);
        } else {
            return $this->json([
                'error' => $result['message'],
            ], 400);
        }
    }

    #[Route('/user', methods: ['DELETE'])]
    public function delete(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof UserEntity) {
            return $this->json(['error' => 'Not authenticated.'], 401);
        }

        $result = $this->userService->deleteUser($user);

        if (!empty($result['success'])) {
            return $this->json(['message' => $result['message']]);
        }

        return $this->json(['error' => $result['message'] ?? 'Unable to delete user.'], 400);
    }

    #[Route('/login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
    }

}
