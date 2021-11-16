<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    /**
     * @Route("/task/listing", name="task")
     */
    public function index(): Response
    {
        //Recuperation du répository de nos Tasks avec Doctrine
        $repository = $this->getDoctrine()->getRepository(Task::class);

        //récupération de toutes les données
        $tasks = $repository->findAll();

        //Affichage des données
        //dump($tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/task/create", name="task_create")
     */
    public function createTask(Request $request)
    {
        $task = new Task;
        $form = $this->createForm(TaskType::class, $task, []);
        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
