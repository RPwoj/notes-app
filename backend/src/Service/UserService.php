<?php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    )
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
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $dto->password
        );
        $user->setPassword($hashedPassword);

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