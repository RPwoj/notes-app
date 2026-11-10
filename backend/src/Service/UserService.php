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
    
    public function createUser(object $dto): array
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
        $user->setIsConfirmed(false);
        $confirmationToken = bin2hex(random_bytes(32));
        $user->setConfirmationToken($confirmationToken);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if (!$user->getId()) {
            return [
                'error' => 'Failed to create user.',
            ];
        }

        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'confirmationToken' => $user->getConfirmationToken(),
            'message' => 'User ' . $user->getEmail() . ' created successfully.'
        ];
    }

    public function confirmUser(?string $token): array
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid confirmation token.'
            ];
        }

        if ($user->getPendingEmail() !== null) {
            $existingUser = $userRepository->findOneBy(['email' => $user->getPendingEmail()]);
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                return [
                    'success' => false,
                    'message' => 'This email address is no longer available.'
                ];
            }

            $user->setEmail($user->getPendingEmail());
            $user->setPendingEmail(null);
            $user->setConfirmationToken(null);
            $this->entityManager->flush();

            return [
                'success' => true,
                'message' => 'Email address updated successfully.'
            ];
        }

        $user->setIsConfirmed(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();

        return [
            'success' => true,
            'message' => 'User confirmed successfully.'
        ];
    }

    public function editUser(?User $user, object $dto): array
    {
        if (!$user) {
            return [
                'success' => false,
                'message' => 'You must be logged in to update your account.'
            ];
        }

        if (!$dto) {
            return [
                'success' => false,
                'message' => 'Account update data is missing.'
            ];
        }

        $passwordProvided = $dto->password !== null || $dto->passwordRepeated !== null;
        $emailProvided = $dto->email !== null;

        if (!$passwordProvided && !$emailProvided) {
            return [
                'success' => false,
                'message' => 'Provide a new email address or password.'
            ];
        }

        if ($passwordProvided && (!$dto->password || $dto->password !== $dto->passwordRepeated)) {
            return [
                'success' => false,
                'message' => 'Passwords do not match.'
            ];
        }

        if ($emailProvided && (!$dto->email || trim($dto->email) === '')) {
            return [
                'success' => false,
                'message' => 'A new email address is required.'
            ];
        }

        $pendingEmail = null;
        $confirmationToken = null;
        if ($emailProvided && $dto->email !== $user->getEmail()) {
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email]);
            if ($existingUser) {
                return [
                    'success' => false,
                    'message' => 'This email address is already in use.'
                ];
            }

            $pendingEmail = $dto->email;
            $confirmationToken = bin2hex(random_bytes(32));
            $user->setPendingEmail($pendingEmail);
            $user->setConfirmationToken($confirmationToken);
        }

        if ($passwordProvided) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $dto->password
            );
            $user->setPassword($hashedPassword);
        }

        if ($passwordProvided || $pendingEmail !== null) {
            $this->entityManager->flush();
        }

        $message = match (true) {
            $passwordProvided && $pendingEmail !== null => 'Password updated successfully. A confirmation email was sent to your new address.',
            $passwordProvided => 'Password updated successfully.',
            $pendingEmail !== null => 'A confirmation email was sent to your new address.',
            default => 'No changes were made.'
        };

        $result = [
            'success' => true,
            'message' => $message
        ];

        if ($pendingEmail !== null && $confirmationToken !== null) {
            $result['emailConfirmationToken'] = $confirmationToken;
            $result['pendingEmail'] = $pendingEmail;
        }

        return $result;
    }

    public function deleteUser(?User $user): array
    {
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not authenticated.'
            ];
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return [
            'success' => true,
            'message' => 'User ' . $user->getEmail() . ' deleted successfully.'
        ];
    }
}