<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        //Mise en place du Faker en FR
        $faker = \Faker\Factory::create('fr_FR');

        $etat = $manager->getRepository(Etat::class)->findAll();
        $lieu = $manager->getRepository(Lieu::class)->findAll();
        $participant = $manager->getRepository(Participant::class)->findAll();
        $campus = $manager->getRepository(Campus::class)->findAll();

        //Création d'Exemples de données de Sorties
        for ($i=1; $i<= 10; $i++){
            $sortie=new Sortie();
            $sortie->setNom($faker->word());
            $sortie->setDateHeureDebut($faker->dateTimeBetween('- 6 months', 'now'));
            $sortie->setDuree($faker->dateTime());
            $sortie->setDateLimiteInscription($faker->dateTimeBetween($sortie->getDateHeureDebut(), 'now'));
            $sortie->setNbInscriptionsMax($faker->numberBetween(1,10));
            $sortie->setInfosSortie($faker->text(200));
            $sortie->setEtat($etat[mt_rand(0, count($etat) -1)]);
            $sortie->setLieuSortie($lieu[mt_rand(0, count($lieu) -1)]);
            $sortie->setOrganisateur($participant[mt_rand(0, count($participant) -1)]);
            $sortie->setSiteOrganisateur($campus[mt_rand(0, count($campus) -1)]);
            $manager->persist($sortie);

        }

        $manager -> flush();
    }

    public function getDependencies():array
    {
        return [EtatFixture::class,LieuFixture::class,CampusFixture::class,ParticipantFixture::class];
    }
}