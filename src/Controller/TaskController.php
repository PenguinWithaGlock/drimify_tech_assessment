<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class TaskController extends AbstractController
{
    #[Route('/task', name: 'list_tasks', methods: ['GET'])]
    public function list(TaskRepository $taskRepo): JsonResponse
    {
        $tasks = $taskRepo->findBy(['deletedAt' => null]);
        $data = [];

        foreach ($tasks as $task) {
            $data[] = [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'isDone' => $task->getIsDone(),
                'createdAt' => $task->getCreatedAt()?->format('Y-m-d H:i:s'),
                'updatedAt' => $task->getUpdatedAt()?->format('Y-m-d H:i:s'),
                'deletedAt' => $task->getDeletedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/tasks', name: 'create_task', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $task = new Task();
        $task->setTitle($data['title']);
        $em->persist($task); 
        $em->flush(); 

        return new JsonResponse(['message' => 'Task created successfully!']);
    }

    #[Route('/tasks/{id}/toggle', name: 'toggle_done_task', methods: ['POST'])]
    public function toggleDone(Task $task, EntityManagerInterface $em): JsonResponse
    {
        $task->setIsDone(!$task->getIsDone());
        $task->setUpdatedAt(new \DateTime());
        $em->flush();

        return new JsonResponse(['message' => 'Toggled task status',]);
    }

    #[Route('/tasks/{id}/delete', name: 'delete_task', methods: ['POST'])]
    public function toggleDelete(Task $task, EntityManagerInterface $em): JsonResponse
    {
        $task->setDeletedAt(new \DateTime());
        $task->setUpdatedAt(new \DateTime());
        $em->flush();

        return new JsonResponse(['message' => 'Task deleted successfully',]);
    }
}
