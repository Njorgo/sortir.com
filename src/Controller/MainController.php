<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController {

    #[Route("/", name:"main_home")]

    public function home() {

        return $this->render("main/home.html.twig");
    }

    #[Route('/donnees', name: 'main_donnees')]
    public function affichageDonnees(
        CampusRepository $campusRepository,
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository,
        EtatRepository $etatRepository,
        LieuRepository $lieuRepository,
        VilleRepository $villeRepository
    ){
        /* Pour faire des requêtes sur la table Sortie */
        $sortie = $sortieRepository-> findBy(['siteOrganisateur' => '2' ],['nbInscriptionsMax' =>'ASC']);

        /* Pour faire des requêtes sur la table Campus */
        $campus = $campusRepository-> findBy([], ['id' =>'ASC']);

        /* Pour faire des requêtes sur la table Participants */
        $participant = $participantRepository-> findBy(['administrateur' =>'1'],['mail' =>'ASC'] );

        /* Pour faire des requêtes sur la table Etat */
        $etat = $etatRepository-> findBy([], ['libelle' =>'ASC']);

        /* Pour faire des requêtes sur la table Lieu */
        $lieu = $lieuRepository-> findBy([], ['nom' =>'ASC'], ['rue' =>'ASC']);

        /* Pour faire des requêtes sur la table Ville */
        $ville = $villeRepository-> findBy([],['codePostal' =>'DESC']);

        return $this->render('main/donnees.html.twig', [
            'sortie' => $sortie,
            'campus' => $campus,
            'participant' => $participant,
            'etat' => $etat,
            'lieu' => $lieu,
            'ville' => $ville
        ]);

    }
}