<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // créer un objet faker
        $faker = Factory::create('fr_FR');

        //création entre 15 et 30 tâches aléatoire
        for ($t = 0; $t < mt_rand(15, 30); $t++) {
            //nouvel objet task
            $task = new Task;

            //nourrir l'objet
            $task->setName($faker->sentence(6))
                ->setDescription($faker->paragraph(3))
                ->setCreatedAt(new \DateTime())
                ->setDueAt($faker->dateTimeBetween('now', '6 months'));

            //faire persister les données
            $manager->persist($task);
        }

        $manager->flush();
    }
}
