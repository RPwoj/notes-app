<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Dto\UserPostDto;
use App\Service\UserService;

final class UserController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private UserService $userService
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
}
