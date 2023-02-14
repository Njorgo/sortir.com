<?php

namespace App\Service;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class GestionEtatSortie {

    protected $sortie;
    protected $etat;
    protected $entity;    

    public function __construct(SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager)
    {
        $this->sortie = $sortieRepository;
        $this->etat = $etatRepository;
        $this->entity = $entityManager;
    }

    public function verifierEtat(): void
    {
        $sorties = $this->sortie->findAll();
        $now = new \DateTime();
        $now->modify('+ 1 hour');

        foreach ($sorties as $value) {
            if ($value->getEtat() != 'Annulée' and $value->getEtat() != 'Créée') {

                $dateHeureDebut = $value->getDateHeureDebut();
                if ($value->getNbInscriptionsMax() == $value->getInscrits()->count() or $value->getDateLimiteInscription() < $now) {
                    $value->setEtat(($this->etat->findOneBy(['libelle' => 'Clôturée'])));
                } else {
                    $value->setEtat(($this->etat->findOneBy(['libelle' => 'Ouverte'])));
                }

                if ($dateHeureDebut <= $now) {
                    $value->setEtat($this->etat->findOneBy(['libelle' => 'Activité en cours']));
                    $this->entity->persist($value);
                    $this->entity->flush();
                }

                if ($dateHeureDebut->modify('+ ' . $value->getDuree() . 'minutes') <= $now) {
                    $value->setEtat($this->etat->findOneBy(['libelle' => 'Passée']));

                }
                $dateHeureDebut->modify('- ' . $value->getDuree() . 'minutes');

                $this->entity->persist($value);
                $this->entity->flush();
            }
        }

    }
}