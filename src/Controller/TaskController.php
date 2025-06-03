<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TaskController extends AbstractController
{
    #[Route('/task', name: 'list_tasks', methods: ['GET'])]
    public function list(TaskRepository $taskRepo): Response
    {
        $tasks = $taskRepo->findBy(['deletedAt' => null]);

        return $this->render('task/index.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks', name: 'create_task', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $title = $request->request->get('title');

        $task = new Task();
        $task->setTitle($title);
        $em->persist($task); 
        $em->flush(); 

        return $this->redirectToRoute('list_tasks');
    }

    #[Route('/tasks/{id}/toggle', name: 'toggle_done_task', methods: ['POST'])]
    public function toggleDone(Task $task, EntityManagerInterface $em): Response
    {
        $task->setIsDone(!$task->getIsDone());
        $task->setUpdatedAt(new \DateTime());
        $em->flush();

        return $this->redirectToRoute('list_tasks');
    }

    #[Route('/tasks/{id}/delete', name: 'delete_task', methods: ['POST'])]
    public function toggleDelete(Task $task, EntityManagerInterface $em): Response
    {
        $task->setDeletedAt(new \DateTime());
        $task->setUpdatedAt(new \DateTime());
        $em->flush();

        return $this->redirectToRoute('list_tasks');
    }
}
