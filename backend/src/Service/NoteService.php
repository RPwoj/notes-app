<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Note;
use DateTime;
use DateTimeZone;

class NoteService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createNote(object $data)
    {
        $datetime = new DateTime('now', new DateTimeZone('Europe/Warsaw'));
        $note = new Note();
        $note->setTitle($data->name);
        $note->setCreated($datetime);
        $note->setEdited($datetime);

        $this->entityManager->persist($note);
        $this->entityManager->flush();

        return $note->getId();
    }
}