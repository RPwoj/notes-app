<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Dto\UserPostDto;
use App\Dto\UserPatchDto;
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
        private EmailService $emailService,
        #[Autowire('%env(BACKEND_URL)%')]
        private string $backendUrl,
        #[Autowire('%env(FRONTEND_URL)%')]
        private string $frontendUrl
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
        if (isset($data['error'])) {
            return $this->json(['error' => $data['error']], 400);
        }
        
        $confirmationToken = $data['confirmationToken'] ?? null;
        if (!$confirmationToken) {
            return $this->json(['error' => 'Failed to create user.'], 400);
        }

        $this->emailService->sendEmail(
            $dto->email,
            'Confirm your account',
            'Please click the following link to confirm your account: <a href="' . rtrim($this->backendUrl, '/') . '/confirm?token=' . rawurlencode($confirmationToken) . '">Confirm Account</a>'
        );

        return new JsonResponse($data);
    }

    #[Route('/confirm', methods: ['GET'])]
    public function confirm(Request $request): Response
    {
        $token = $request->query->get('token');

        if (!$token) {
            return $this->json([
                'error' => 'Token is required.',
            ], 400);
        }

        $result = $this->userService->confirmUser($token);

        if ($result['success']) {
            return $this->redirect(rtrim($this->frontendUrl, '/') . '/login?confirmed=1');
        } else {
            return $this->json([
                'error' => $result['message'],
            ], 400);
        }
    }

    #[Route('/edit-user', methods: ['PATCH'])]
    public function editProfile(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof UserEntity) {
            return $this->json(['error' => 'You must be logged in to update your account.'], 401);
        }

        $requestData = json_decode($request->getContent(), true);
        $dto = new UserPatchDto();
        $dto->email = $requestData['email'] ?? null;
        $dto->password = $requestData['password'] ?? null;
        $dto->passwordRepeated = $requestData['password-repeated'] ?? null;

        $errors = $this->validator->validate($dto);
        $errorsArray = [];
        
        if (count($errors) > 0) {
            foreach($errors as $error) {
                $errorsArray[] = $error->getMessage();
            }

            return $this->json(['errors' => $errorsArray], 400);
        }

        $result = $this->userService->editUser($user, $dto);

        if (!empty($result['success'])) {
            if (isset($result['emailConfirmationToken'], $result['pendingEmail'])) {
                $this->emailService->sendEmail(
                    $result['pendingEmail'],
                    'Confirm your new email address',
                    'Please confirm your new email address by clicking the following link: <a href="' . rtrim($this->backendUrl, '/') . '/confirm?token=' . rawurlencode($result['emailConfirmationToken']) . '">Confirm email address</a>'
                );
            }

            return $this->json(['message' => $result['message']]);
        }

        return $this->json(['error' => $result['message'] ?? 'Unable to update your account.'], 400);
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

    #[Route('/check-login', methods: ['GET'])]
    public function checkLogin(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof UserEntity) {
            return $this->json(['authenticated' => false], 401);
        }

        return $this->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getUserIdentifier(),
            ],
        ]);
    }

    #[Route('/login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
    }

}
