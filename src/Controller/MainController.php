<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController {

    #[Route("/", name:"main_home")]

    public function home() {

        return $this->render("main/home.html.twig");
    }

    #[Route('/profil/{participantId}', name: 'main_profil')]
    public function profilParticipant(int $participantId, ParticipantRepository $participantRepository)
    {
        $participant = $participantRepository->find($participantId);

        if (!$participant){
            throw $this->createNotFoundException('Erreur 404 :Utilisateur Inexistant');
        }

        return $this->render('main/profil.html.twig', [
            'participant'=>$participant
        ]);
    }
}