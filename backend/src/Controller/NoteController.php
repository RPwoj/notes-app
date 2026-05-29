<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Dto\NoteUpdateDto;
use App\Service\NoteService;


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
    public function create(Request $request, NoteService $note): JsonResponse
    {
        $data = json_decode($request->getContent());
        $result = $note->createNote($data);

        if ($result['success']) {
            return $this->json([
                'id' => $result['id'],
            ]);
        } else {
            return $this->json([
                'error' => $result['errors'],
            ], 400);
        }
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

    #[Route('/note',  methods: ['DELETE'])]
    public function delete(): JsonResponse
    {
        return $this->json([
            'message' => 'delete',
            'path' => 'src/Controller/NoteController.php',
        ]);
    }
}
