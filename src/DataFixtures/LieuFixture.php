<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        //Mise en place du Faker en FR
        $faker = \Faker\Factory::create('fr_FR');

        $ville = $manager->getRepository(Ville::class)->findAll();

        for ($i=1; $i<= 10; $i++){

            $lieu=new Lieu();
            $lieu->setNom($faker->word());
            $lieu->setRue($faker->address());
            $lieu->setVille($ville[mt_rand(0, count($ville) -1)]);
            $lieu->setLatitude($faker->latitude());
            $lieu->setLongitude($faker->longitude());
            $manager->persist($lieu);


        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [VilleFixture::class];
    }
}
