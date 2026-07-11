<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Dto\NoteUpdateDto;
use App\Service\NoteService;
use DateTime;
use DateTimeZone;
use App\Entity\Note;
use App\Dto\NotePostDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;


final class NoteController extends AbstractController
{
    #[Route('/note',  methods: ['GET'])]
    public function index(NoteService $note): JsonResponse
    {
        $result = $note->getAllNotesData();

        return $this->json([
            'data' => $result,
        ]);
    }

    #[Route('/note/{id}',  methods: ['GET'])]
    public function show(int $id, NoteService $note): JsonResponse
    {
        $result = $note->getNote($id);
        
        if ($result) {
            return $this->json([
                'data' => $result,
            ]);
        } else {
            return $this->json([
                'error' => 'Note with id ' . $id . ' doesnt exists',
            ], 400);
        }
    }

    #[Route('/note',  methods: ['POST'])]
    public function create(Request $request, NoteService $noteService, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json([
                'error' => 'Invalid JSON.',
            ], 400);
        }

        $dto = new NotePostDto();
        $dto->title = $data['title'] ?? null;
        $dto->content = $data['content'] ?? null;
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $formattedErrors = [];

            foreach ($errors as $error) {
                $formattedErrors[] = $error->getMessage();
            }

            return $this->json([
                'error' => $formattedErrors,
            ], 400);
        }

        $result = $noteService->createNote($dto);
        
        if (!$result) {
            return $this->json([
                'error' => 'Unable to create note.',
            ], 500);
        }

        return $this->json([
            'id' => $result['id'],
        ], 201);
    }

    #[Route('/note/{id}',  methods: ['PATCH'])]
    public function update(int $id, Request $request, NoteService $note): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $noteUpdateDto = new NoteUpdateDto();
        $noteUpdateDto->title = (array_key_exists('title', $data)) ? $data['title'] : null;
        $noteUpdateDto->content = (array_key_exists('content', $data)) ? $data['content'] : null;

        $result = $note->updateNote($id, $noteUpdateDto);
        $response = [];

        foreach($result as $key => $value) {
            $response[$key] = $value;
        }

        return $this->json(
            $response
        );
    }

    #[Route('/note/{id}',  methods: ['DELETE'])]
    public function delete(int $id, NoteService $noteService): JsonResponse
    {
        $result = $noteService->deleteNote($id);

        if ($result['success']) {
            return $this->json([
                'message' => $result['message'],
            ]);
        } else {
            return $this->json([
                'error' => $result['error'],
            ], 400);
        }
    }
}
