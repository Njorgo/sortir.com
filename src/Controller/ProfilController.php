<?php

namespace App\Controller;

use App\Form\ProfilParticipantConnecteType;
use App\Repository\ParticipantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/', name: 'profil_participant_')]
class ProfilController extends AbstractController
{
    #[Route('/profil/{participantId}', name: '')]
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

    #[Route('/monProfil', name: 'Connecte')]
    public function profilParticipantConnecte(Request $request) : Response
    {
        $participantForm = $this->createForm(ProfilParticipantConnecteType::class );
        $participantForm->handleRequest($request);
        return $this->render('main/profilParticipantConnecte.html.twig', [
            'participantForm' => $participantForm->createView()
        ]);
    }
}
