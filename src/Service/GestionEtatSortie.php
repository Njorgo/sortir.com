<?php

namespace App\Service;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class GestionEtatSortie {

    public function majEtats(EtatRepository $etatRepository, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): void
    {
        $sorties = $sortieRepository->listeSorties();
        $dateJour = new \DateTime();
        $dateArchive = new \DateTime('-1 month');

        foreach ($sorties as $sortieMaj) {
            $sortie = $sortieRepository->find($sortieMaj);

            $dateFinSortie = $sortie->getDateHeureDebut()->getTimestamp() + $sortie->getDuree();

            if ($dateFinSortie <= $dateArchive->getTimestamp()) {
                $sortie->setEtat($etatRepository->find('Archivée'));
                $entityManager->persist($sortie);

            } elseif ($dateFinSortie <= $dateJour->getTimestamp()) {
                $sortie->setEtat($etatRepository->find('Passée'));
                $entityManager->persist($sortie);

            }elseif($sortie->getDateLimiteInscription()->getTimestamp() <= $dateJour->getTimestamp()){
                $sortie->setEtat($etatRepository->find('Clôturée'));
                $entityManager->persist($sortie);
            }
            if($sortie->getInscrits()->count() == $sortie->getNbInscriptionsMax()){
                $sortie->setEtat($etatRepository->find('Clôturée'));
                $entityManager->persist($sortie);
            }elseif($sortie->getInscrits()->count() < $sortie->getNbInscriptionsMax()){
                $sortie->setEtat($etatRepository->find('Ouverte'));
                $entityManager->persist($sortie);
            }
        }
        $entityManager->flush();
    }
}