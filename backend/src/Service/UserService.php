<?php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;


class UserService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }
    
    public function createUser($dto): array
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $userExists = $userRepository->findOneBy(['email' => $dto->email]);
        if ($userExists) {
            return [
                'error' => 'User with email ' . $dto->email . ' already exists.',
            ];
        }

        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword($dto->password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if (!$user->getId()) {
            return [
                'error' => 'Failed to create user.',
            ];
        }

        return [
            'id' => $user->getId(),
            'message' => 'User ' . $user->getEmail() . ' created successfully.',
        ];
    }
}