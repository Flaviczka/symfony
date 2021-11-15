<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        dd($tasks);

        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
}
