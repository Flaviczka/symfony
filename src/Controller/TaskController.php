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
     * @Route("/task/listing", name="task_listing")
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
    public function createTask(Request $request): Response
    {
        $task = new Task;
        $task->setCreatedAt(new \DateTime());

        $form = $this->createForm(TaskType::class, $task, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* $task->setName($form['name']->getDaTa())
                ->setDescription($form['description']->getDaTa())
                ->setDueAt($form['dueAt']->getDaTa())
                ->setTag($form['tag']->getDaTa()); */

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($task);
            $manager->flush();

            return $this->redirectToRoute("task_listing");
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/task/update/{id}", name="task_update", requirements={"id"="\d+"})
     */
    public function updateTask(int $id, Request $request): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $task = $manager->getRepository(Task::class)->find($id);
        //$task = $manager->getRepository(Task::class)->findOneBy(['id']);
        $form = $this->createForm(TaskType::class, $task, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* $task->setName($form['name']->getDaTa())
                ->setDescription($form['description']->getDaTa())
                ->setDueAt($form['dueAt']->getDaTa())
                ->setTag($form['tag']->getDaTa()); */


            $manager->flush();

            return $this->redirectToRoute("task_listing");
        }

        return $this->render('task/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
