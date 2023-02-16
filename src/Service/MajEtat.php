<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class MajEtat
{
    public function __construct(private SortieRepository $sortieRepository,
                                private EntityManagerInterface $entityManager,
                                private EtatRepository $etatRepository)
    {

    }

    public function majEtat(){

        $sorties= $this->sortieRepository->listeSorties();
        $dateJour = new \DateTime();
        $dateArchive = new \DateTime('-1 month');

        //définition des variables états
        $etatArchivee = $this->etatRepository->findOneBy(['libelle' => 'Archivée']);
        $etatCloturee = $this->etatRepository->findOneBy(['libelle' => 'Clôturée']);
        $etatPassee = $this->etatRepository->findOneBy(['libelle' => 'Passée']);
        $etatOuverte = $this->etatRepository->findOneBy(['libelle' => 'Ouverte']);
        $etatEnCours = $this->etatRepository->findOneBy(['libelle' => 'Activité en cours']);
        $etatCreee = $this->etatRepository->findOneBy(['libelle'=> 'Créée']);
        $etatAnnulee = $this->etatRepository->findOneBy(['libelle' => 'Annulée']);

        foreach ($sorties as $sortieMaj) {
            $sortie = $this->sortieRepository->find($sortieMaj);

            $dateFinSortie = $sortie->getDateHeureDebut()->getTimestamp() + $sortie->getDuree();
            $dateDebut = $sortie->getDateHeureDebut()->getTimestamp();


            if ($dateFinSortie <= $dateArchive->getTimestamp()) {
                $sortie->setEtat($this->etatRepository->find($etatArchivee));
                $this->entityManager->persist($sortie);

            }elseif ($sortie->getEtat() === $etatCreee){
                $sortie->setEtat($this->etatRepository->find($etatCreee));

            }elseif ($sortie->getEtat() === $etatAnnulee){
                $sortie->setEtat($this->etatRepository->find($etatAnnulee));

            }elseif ($dateFinSortie <= $dateJour->getTimestamp()) {
                $sortie->setEtat($this->etatRepository->find($etatPassee));
                $this->entityManager->persist($sortie);

            }elseif($sortie->getInscrits()->count() < $sortie->getNbInscriptionsMax() && $dateJour->getTimestamp() < $dateDebut){
                $sortie->setEtat($this->etatRepository->find($etatOuverte));
                $this->entityManager->persist($sortie);

            }elseif ($dateFinSortie > $dateJour->getTimestamp() && $dateJour->getTimestamp() > $dateDebut ){
                $sortie->setEtat($this->etatRepository->find($etatEnCours));
                $this->entityManager->persist($sortie);

            }elseif($sortie->getDateLimiteInscription()->getTimestamp() <= $dateJour->getTimestamp() || $sortie->getInscrits()->count() == $sortie->getNbInscriptionsMax()){
                $sortie->setEtat($this->etatRepository->find($etatCloturee));
                $this->entityManager->persist($sortie);

            }
        }
        $this->entityManager->flush();
    }
}