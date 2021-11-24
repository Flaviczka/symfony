<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Faker\Factory;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Migrations\Version\State;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppFixtures extends Fixture
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder, TranslatorInterface $translator)
    {
        $this->encoder = $encoder;
        $this->translator = $translator;
    }

    public function load(ObjectManager $manager): void
    {

        // créer un objet faker
        $faker = Factory::create('fr_FR');

        //creation de 5 utilisateurs
        for ($u = 0; $u < 5; $u++) {
            $user = new User;

            //hashage du mdp avec les param de sécu du $encoder
            $hash = $this->encoder->hashPassword($user, "password");
            $user->setPassword($hash);
            //si 1er user, rôle => admin
            if ($u === 0) {
                $user->setRoles(["ROLE_ADMIN"])
                    ->setEmail("admin@admin.fr");
            } else {
                $user->setEmail($faker->safeEmail());
            }
            $manager->persist($user);
        }

        //creation de nos 5 catégories
        for ($c = 0; $c < 5; $c++) {
            //creation d'un nouvel objet Tag
            $tag = new Tag;

            //nourrir l'objet
            $tag->setName($faker->colorName());

            $manager->persist($tag);
        }

        // Statut « à faire »
        $todo = new Status;
        // Label identifiable facilement
        $todo->setLabel('1');
        // faire persister l’objet
        $manager->persist($todo);

        // Statut « en cours »
        $wip = new Status;
        // Label identifiable facilement
        $wip->setLabel('2');
        // faire persister l’objet
        $manager->persist($wip);

        // Statut « terminée »
        $done = new Status;
        // Label identifiable facilement
        $done->setLabel('3');
        // faire persister l’objet
        $manager->persist($done);

        //on push les categorie en BDD
        $manager->flush();

        //récupération des catégories créées
        $tags = $manager->getRepository(Tag::class)->findAll();
        $status = $manager->getRepository(Status::class)->findAll();
        $listUsers = $manager->getRepository(User::class)->findAll();

        //création entre 15 et 30 tâches aléatoire
        for ($t = 0; $t < mt_rand(15, 30); $t++) {
            //nouvel objet task
            $task = new Task;

            //nourrir l'objet
            $task->setName($faker->sentence(6))
                ->setDescription($faker->paragraph(3))
                ->setCreatedAt(new \DateTime())
                ->setDueAt($faker->dateTimeBetween('now', '6 months'))
                ->setTag($faker->randomElement($tags))
                ->setStatus($faker->randomElement($status))
                ->setUser($faker->randomElement($listUsers));

            //faire persister les données
            $manager->persist($task);
        }

        $manager->flush();
    }
}
