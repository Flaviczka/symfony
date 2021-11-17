<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManager;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TaskController extends AbstractController
{
    /**
     * @var TaskRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(TaskRepository $repository, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }


    /**
     * @Route("/task/listing", name="task_listing")
     */
    public function index(): Response
    {
        //Recuperation des infos user
        //$user = $this->getUser();
        //dd($user);
        //Recuperation du répository de nos Tasks avec Doctrine
        //$repository = $this->getDoctrine()->getRepository(Task::class);

        //récupération de toutes les données
        $tasks = $this->repository->findAll();

        //Affichage des données
        //dump($tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }


    /**
     * @Route("/task/create", name="task_create")
     */
    /*
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

    //$manager = $this->getDoctrine()->getManager();
    /* $this->manager->persist($task);
            $this->manager->flush();

            return $this->redirectToRoute("task_listing");
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    } */


    /**
     * @Route("/task/update/{id}", name="task_update", requirements={"id"="\d+"})
     */
    /*public function updateTask(int $id, Request $request): Response
    {
        //$manager = $this->getDoctrine()->getManager();
        $task = $this->repository->find($id);
        //$task = $manager->getRepository(Task::class)->findOneBy(['id']);
        $form = $this->createForm(TaskType::class, $task, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* $task->setName($form['name']->getDaTa())
                ->setDescription($form['description']->getDaTa())
                ->setDueAt($form['dueAt']->getDaTa())
                ->setTag($form['tag']->getDaTa()); */


    /*    $this->manager->flush();

            return $this->redirectToRoute("task_listing");
        }

        return $this->render('task/update.html.twig', [
            'form' => $form->createView()
        ]);
    } */

    /**
     * @Route("/task/create", name="task_create")
     * @Route("/task/update/{id}", name="task_update", requirements={"id"="\d+"})
     */
    public function task(Task $task = null, Request $request): Response
    {
        if (!$task) {
            $task = new Task;
            $task->setCreatedAt(new \DateTime());
        }


        $form = $this->createForm(TaskType::class, $task, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* $task->setName($form['name']->getDaTa())
                ->setDescription($form['description']->getDaTa())
                ->setDueAt($form['dueAt']->getDaTa())
                ->setTag($form['tag']->getDaTa()); */

            //$manager = $this->getDoctrine()->getManager();
            $this->manager->persist($task);
            $this->manager->flush();

            return $this->redirectToRoute("task_listing");
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/task/delete/{id}", name="task_delete", requirements={"id"="\d+"})
     */
    public function deleteTask(Task $task): Response
    {
        $this->manager->remove($task);
        $this->manager->flush();

        return $this->redirectToRoute("task_listing");
    }
}
