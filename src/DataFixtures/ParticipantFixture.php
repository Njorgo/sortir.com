<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ParticipantFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        //Mise en place du Faker en FR
        $faker = \Faker\Factory::create('fr_FR');

        $campus = $manager->getRepository(Campus::class)->findAll();



        //Création d'exemples de données de Participants
        for ($i=1; $i<= 10; $i++){
                $participant=new Participant();
                $participant->setPseudo($faker->name());
                $participant->setNom($faker->lastName());
                $participant->setPrenom($faker->firstName());
                $participant->setTelephone($faker->phoneNumber());
                $participant->setMail($faker->email());
                $participant->setMotPasse($faker->sha1());
                $participant->setAdministrateur($faker->boolean());
                $participant->setActif($faker->boolean());
                $participant->setCampus($campus[(mt_rand(0, count($campus)-1))]);
                $manager->persist($participant);

        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CampusFixture::class];
    }
}
