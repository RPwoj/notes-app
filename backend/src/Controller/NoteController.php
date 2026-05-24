<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
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
                'excerpt' => $result['excerpt'],
            ]);
        } else {
            return $this->json([
                'error' => $result['errors'],
            ], 400);
        }
    }

    #[Route('/note',  methods: ['PATCH'])]
    public function update(): JsonResponse
    {
        return $this->json([
            'message' => 'patch',
            'path' => 'src/Controller/NoteController.php',
        ]);
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
