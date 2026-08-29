<?php
namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\Note;
use App\Entity\User;

class NoteVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    
    public function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE]) && $subject instanceof Note;
    }
        
    public function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        /** @var Note $note */
        $note = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($note, $user);
            case self::EDIT:
                return $this->canEdit($note, $user);
            case self::DELETE:
                    return $this->canDelete($note, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Note $note, User $user): bool
    {
        if ($note->getOwner() === $user) {
            return true;
        }

        return false;
    }

    private function canEdit(Note $note, User $user): bool
    {
        if ($note->getOwner() === $user) {
            return true;
        }

        return false;
    }

    private function canDelete(Note $note, User $user): bool
    {
        if ($note->getOwner() === $user) {
            return true;
        }

        return false;
    }
}
