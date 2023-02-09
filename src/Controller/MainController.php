<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController {

    #[Route("/", name:"main_home")]

    public function home() {

        return $this->render("main/home.html.twig");
    }

    #[Route('/donnees', name: 'main_donnees')]
    public function affichageDonnees(SortieRepository $sortieRepository){

        /* Affichage des informations demandés pour les différentes sorties proposées */
        $infosSortie = $sortieRepository-> affichageInfosSorties();

        return $this->render('main/donnees.html.twig', [
            'infosSortie' => $infosSortie,
        ]);

    }
}