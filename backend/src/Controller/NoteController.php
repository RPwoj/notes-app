<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\NoteService;


final class NoteController extends AbstractController
{
    #[Route('/note',  methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'get',
            'path' => 'src/Controller/NoteController.php',
        ]);
    }

    #[Route('/note/{id}',  methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        return $this->json([
            'message' => 'get' . $id,
            'path' => 'src/Controller/NoteController.php',
        ]);
    }

    #[Route('/note',  methods: ['POST'])]
    public function create(Request $request, NoteService $note): JsonResponse
    {
        $data = json_decode($request->getContent());
        
        return $this->json([
            'message' => $note->createNote($data),
            'path' => 'src/Controller/NoteController.php',
        ]);
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
