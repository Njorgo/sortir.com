<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        //Mise en place du Faker en FR
        $faker = \Faker\Factory::create('fr_FR');

        for ($i=1; $i<= 10; $i++){
            $campus=new Campus();
            $campus->setNom($faker->city());
            $manager->persist($campus);
        }

        $manager->flush();
    }
}
