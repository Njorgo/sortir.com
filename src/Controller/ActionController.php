<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class ActionController extends AbstractController {

    #[Route("/", name:"main_donnees")]

    public function actions(EtatRepository $etatRepository, ParticipantRepository $participantRepository, SortieRepository $sortieRepository) {

        /* affichage de l'action selon état en cours */
        $actionSelonEtat = $etatRepository->affichageEtat();

        return $this->render("main/donnees.html.twig", [
            'actionSelonEtat' => $actionSelonEtat,
        ]);
    }

}