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

    public function getAllNotesData(): ?array
    {
        $result = [];

        $notes = $this->entityManager->getRepository(Note::class)->findAll();
        if (!$notes) return null;

        foreach($notes as $note) {
            $result[] = [
                'title' => $note->getTitle(),
                'excerpt' => $note->getExcerpt(),
                'content' => $note->getContent(),
                'created' => $note->getCreated(),
                'edited' => $note->getEdited(),
                'favorite' => $note->isFavorite(),
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

    public function updateNote(int $id, NoteUpdateDto $dto)
    {
        $datetime = new DateTime('now', new DateTimeZone('Europe/Warsaw'));
        $updated = false;

        $note = $this->entityManager->getRepository(Note::class)->find($id);
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

        if ($dto->title) {
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
            $edited = $note->getEdited($datetime);
            $this->entityManager->persist($note);
            $this->entityManager->flush();

            $result = [
                'success' => true,
                'message' => 'data changed',
                'editedTime' => $edited,
                'changedData' => $changedData
            ];
        } else {
            $result = [
                'success' => true,
                'message' => 'no data changed'
            ];
        }
        
        return $result;
    }

    public function favoriteNote(int $id, ?bool $favorite = null): array
    {
        $note = $this->entityManager->getRepository(Note::class)->find($id);

        if (!$note) {
            return [
                'success' => false,
                'error' => 'No note with id ' . $id,
            ];
        }

        $newFavorite = $favorite === null ? !$note->isFavorite() : $favorite;
        $note->setFavorite($newFavorite);

        $this->entityManager->persist($note);
        $this->entityManager->flush();

        return [
            'success' => true,
            'id' => $id,
            'favorite' => $note->isFavorite(),
        ];
    }

    public function deleteNote(int $id)
    {
        $note = $this->entityManager->getRepository(Note::class)->find($id);

        if ($note) {
            $this->entityManager->remove($note);
            $this->entityManager->flush();
            return [
                'success' => true,
                'message' => 'Deleted note with id ' . $id
            ];

        } else {
            return [
                'success' => false,
                'error' => 'No note with id ' . $id
            ];
        }
    }
}