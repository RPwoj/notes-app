<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Note;
use App\Dto\NotePostDto;
use App\Dto\NoteUpdateDto;
use DateTime;
use DateTimeZone;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\User;

class NoteService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function getAllNotesData(User $user): ?array
    {
        $result = [];

        $notes = $this->entityManager->getRepository(Note::class)->findBy(['owner' => $user]);

        if (!$notes) return null;

        foreach($notes as $note) {
            $result[] = [
                'title' => $note->getTitle(),
                'excerpt' => $note->getExcerpt(),
                'content' => $note->getContent(),
                'created' => $note->getCreated(),
                'edited' => $note->getEdited(),
                'favorite' => $note->isFavorite(),
                'owner' => $note->getOwner()->getEmail(),
            ];
        }

        return $result;
    }

    public function getNote(int $id): ?Note
    {
        return $this->entityManager->getRepository(Note::class)->find($id);
    }

    public function createNote(User $user, NotePostDto $dto)
    {
        $datetime = new DateTime('now', new DateTimeZone('Europe/Warsaw'));
        $note = new Note();
        $note->setTitle($dto->title);   
        $note->setContent($dto->content);
        $note->setCreated($datetime);
        $note->setEdited($datetime);
        $note->setOwner($user);

        $this->entityManager->persist($note);
        $this->entityManager->flush();

        return [
            'success' => true,
            'id' => $note->getId(),
        ];
    }

    public function updateNote(Note $note, NoteUpdateDto $dto)
    {
        $datetime = new DateTime('now', new DateTimeZone('Europe/Warsaw'));
        $updated = false;

        $currentTitle = $note->getTitle();
        $currentContent = $note->getContent();

        $changedData = [];

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $formattedErrors = [];

            foreach ($errors as $error) {
                $formattedErrors[] = $error->getMessage();
            }

            $result = [
                'success' => false,
                'errors' => $formattedErrors,
            ];

            return $result;
        }

        if ($dto->title !== null) {
            if ($currentTitle != $dto->title) {
                $note->setTitle($dto->title);
                $updated = true;
                $changedData['title']['from'] = $currentTitle;
                $changedData['title']['to'] = $dto->title;
            }
        }
            
        if ($dto->content !== null) {
            if ($currentContent !== $dto->content) {
                $note->setContent($dto->content);
                $updated = true;
                $changedData['content']['from'] = $currentContent;
                $changedData['content']['to'] = $dto->content;
            }
        }

        if ($updated) {
            $note->setEdited($datetime);
            $this->entityManager->persist($note);
            $this->entityManager->flush();
            return $note;
        } elseif (!$updated && empty($changedData)) {
            return [
                'success' => false,
                'errors' => 'No changes were made to the note.',
            ];
        }
        
        return false;
    }

    public function favoriteNote(Note $note, ?bool $favorite = null): array
    {
        if (!$note) {
            return [
                'success' => false,
                'error' => 'Note not found.'
            ];
        }

        $newFavorite = $favorite === null ? !$note->isFavorite() : $favorite;
        $note->setFavorite($newFavorite);

        $this->entityManager->persist($note);
        $this->entityManager->flush();

        return [
            'success' => true,
            'favorite' => $note->isFavorite(),
        ];
    }

    public function deleteNote(Note $note)
    {
        if ($note) {
            $this->entityManager->remove($note);
            $this->entityManager->flush();
            return true;
        } else {
            return false;
        }
    }
}