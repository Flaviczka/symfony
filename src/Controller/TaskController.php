<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManager;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Switch_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/task", name="task_")
 */
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
     * @Route("/listing", name="listing")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $role = $user->getRoles();
        $id = $user->getId();
        $admin = 'ROLE_ADMIN';
        $slug = $user->getIsPrefered();

        if (in_array($admin, $role)) {
            //récupération de toutes les données
            $tasks = $this->repository->findBy(['isArchived' => '0']);
        } else {
            $tasks = $this->repository->findBy(['isArchived' => '0', 'user' => $id]);
        }
        //$translator->trans('general.button.delete');
        // $translated = new TranslatableMessage('Symfony is great');
        //Recuperation des infos user
        //$user = $this->getUser();
        //dd($user);
        //Recuperation du répository de nos Tasks avec Doctrine
        //$repository = $this->getDoctrine()->getRepository(Task::class);
        //$this->addFlash('danger', $translator->trans('general.button.delete'));



        //echo $translated;
        //Affichage des données
        //dump($tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks, 'slug' => $slug
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
     * @Route("/create", name="create")
     * @Route("/update/{id}", name="update", requirements={"id"="\d+"})
     */
    public function task(Task $task = null, Request $request, StatusRepository $repository): Response
    {
        if (!$task) {
            $task = new Task;
            $task->setCreatedAt(new \DateTime());
            $user = $this->getUser();
            $task->setUser($user);
        }


        $form = $this->createForm(TaskType::class, $task, []);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {

            // switch ($form['status']->getDaTa()) {
            //     case 1:
            //         $form['status']->setDaTa($repository->find(1));
            //         break;
            //     case 2:
            //         $form['status']->setDaTa($repository->find(2));
            //         break;
            //     case 3:
            //         $form['status']->setDaTa($repository->find(3));
            //         break;
            // }
            /* $task->setName($form['name']->getDaTa())
                ->setDescription($form['description']->getDaTa())
                ->setDueAt($form['dueAt']->getDaTa())
                ->setTag($form['tag']->getDaTa()); */

            //$manager = $this->getDoctrine()->getManager();
            $this->manager->persist($task);
            $this->manager->flush();

            $this->addFlash(
                'success',
                'l\'action a bien été effectuée'
            );

            return $this->redirectToRoute("task_listing");
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id"="\d+"})
     */
    public function deleteTask(Task $task): Response
    {
        $this->manager->remove($task);
        $this->manager->flush();

        $this->addFlash(
            'success',
            'la suppression a bien été effectuée'
        );

        return $this->redirectToRoute("task_listing");
    }

    /**
     * @Route("/listing/download", name="download")
     */
    public function downloadPdf()
    {
        $tasks = $this->repository->findAll();
        // Définition des options du pdf
        $pdfoption = new Options;
        //Police par default
        $pdfoption->set('defaultFont', 'Arial');
        $pdfoption->setIsRemoteEnabled(true);

        // On instancie DOMDF
        $dompdf = new Dompdf($pdfoption);
        //On genére le html
        $html = $this->renderView('pdf/pdfdownload.html.twig', [
            'tasks' => $tasks,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        //On génère un nom de fichier
        $fichier = 'LE PDF OUAIS OUAIS OUAIS CA MARCHE';
        //Envoyer le pdf au navigateur
        $dompdf->stream($fichier, [
            'Attachement' => true
        ]);
        return new Response();
    }

    /**
     * Vérifier si la date effective est passée ou non
     * @param Task $task
     * @return boolean
     */
    public function checkDueAt(Task $task)
    {
        $flag = false;
        $dueAt = $task->getDueAt();
        $today = new \DateTime();

        if ($today > $dueAt) {
            $flag = true;
        }
        return $flag;
    }

    /**
     * @Route("/archives", name="myarchives")
     */
    public function indexArchives(): Response
    {
        $user = $this->getUser();
        $role = $user->getRoles();
        $id = $user->getId();
        $admin = 'ROLE_ADMIN';

        if (in_array($admin, $role)) {
            //récupération de toutes les données
            $tasks = $this->repository->findBy(['isArchived' => '1']);
        } else {
            $tasks = $this->repository->findBy(['isArchived' => '1', 'user' => $id]);
        }
        //$translator->trans('general.button.delete');
        // $translated = new TranslatableMessage('Symfony is great');
        //Recuperation des infos user
        //$user = $this->getUser();
        //dd($user);
        //Recuperation du répository de nos Tasks avec Doctrine
        //$repository = $this->getDoctrine()->getRepository(Task::class);
        //$this->addFlash('danger', $translator->trans('general.button.delete'));



        //echo $translated;
        //Affichage des données
        //dump($tasks);

        return $this->render('task/archives.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/archive/{id}", name="archive", requirements={"id"="\d+"})
     * @return Response
     */
    public function archiveTask(Task $task)
    {
        if ($this->checkDueAt($task)) {
            $task->setIsArchived(1);
            $this->manager->persist($task);
            $this->manager->flush();
            $this->addFlash(
                'success',
                'La tâche a bien été archivée'
            );
        } else {
            $this->addFlash(
                'danger',
                'Impossible d\'archiver une tâche dont l\'échéance n\'est pas dépassée'
            );
        }
        return $this->redirectToRoute('task_listing');
    }

    /**
     * @Route("/archives_{slug}")
     *
     * @param String $slug
     * @return void
     */
    public function displayTable(String $slug)
    {


        //  Récupération des infos de l'utilisateur.
        $user = $this->getUser();


        if ($slug != 'manual') {
            $tasks = $this->repository->findAll();
            $user->setIsPrefered(0);
            for ($i = 0; $i < count($tasks); $i++) {
                if ($this->checkDueAt($tasks[$i])) {
                    $this->archiveTask($tasks[$i]);
                }
            }
        } else {
            $user->setIsPrefered(1);
        }
        $this->manager->persist($user);
        $this->manager->flush();

        return $this->index();
    }
}
