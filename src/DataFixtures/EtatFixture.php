<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //Mise en place du Faker en FR
        $faker = \Faker\Factory::create('fr_FR');

        for ($i=1; $i<= 10; $i++){

            $etat=new Etat();
            $etat->setLibelle($faker->word());
            $manager->persist($etat);

        }

        $manager->flush();
    }
}
