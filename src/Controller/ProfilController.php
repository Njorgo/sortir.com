<?php

namespace App\Controller;

use App\Form\ProfilParticipantConnecteType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'profil_participant_')]
class ProfilController extends AbstractController
{
    #[Route('/profil/{participantId}', name: '')]
    public function profilParticipant(int $participantId, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($participantId);

        if (!$participant) {
            throw $this->createNotFoundException('Erreur 404 :Utilisateur Inexistant');
        }

        return $this->render('profil/profil.html.twig', [
            'participant' => $participant
        ]);
    }

    #[Route('/monProfil', name: 'Connecte')]
    public function profilParticipantConnecte(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher): Response
    {

        $participant = $this->getUser();

        $participantForm = $this->createForm(ProfilParticipantConnecteType::class, $participant);
        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted() && $participantForm->isValid()) {

            $motPasse = $participantForm->get('motPasse')->getData();
            $confirmation = $participantForm->get('confirmation')->getData();

            if ($motPasse === $confirmation && $motPasse !== null) {

                $motPassHash = $passwordHasher->hashPassword(
                    $participant,
                    $motPasse
                );

                $participant->setMotPasse($motPassHash);

                $entityManager->persist($participant);
                $entityManager->flush();

                $entityManager->refresh($participant);

                $this->addFlash('success', 'Profil modifié avec succès!');


            }elseif ($motPasse !== $confirmation && $motPasse!== null){
                $this->addFlash('warning', 'Erreur saisie mot de passe');
                $entityManager->refresh($participant);
            }else{
            $entityManager->persist($participant);
            $entityManager->flush();

            $entityManager->refresh($participant);

            $this->addFlash('success', 'Profil modifié avec succès!');}

        }
        return $this->render('profil/profilParticipantConnecte.html.twig', [
            'participantForm' => $participantForm->createView()
        ]);
    }
}






