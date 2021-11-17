<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        // créer un objet faker
        $faker = Factory::create('fr_FR');

        //creation de nos 5 catégories
        for ($c = 0; $c < 5; $c++) {
            //creation d'un nouvel objet Tag
            $tag = new Tag;

            //nourrir l'objet
            $tag->setName($faker->colorName());

            $manager->persist($tag);
        }

        //on push les categorie en BDD
        $manager->flush();

        //récupération des catégories créées
        $tags = $manager->getRepository(Tag::class)->findAll();

        //création entre 15 et 30 tâches aléatoire
        for ($t = 0; $t < mt_rand(15, 30); $t++) {
            //nouvel objet task
            $task = new Task;

            //nourrir l'objet
            $task->setName($faker->sentence(6))
                ->setDescription($faker->paragraph(3))
                ->setCreatedAt(new \DateTime())
                ->setDueAt($faker->dateTimeBetween('now', '6 months'))
                ->setTag($faker->randomElement($tags));

            //faire persister les données
            $manager->persist($task);
        }

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

        $manager->flush();
    }
}
