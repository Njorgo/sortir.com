<?php

namespace App\Controller;

use App\Form\ProfilParticipantConnecteType;
use App\Form\ResetPasswordType;
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

        if (!$participant){
            throw $this->createNotFoundException('Erreur 404 :Utilisateur Inexistant');
        }

        return $this->render('profil/profil.html.twig', [
            'participant'=>$participant
        ]);
    }

    #[Route('/monProfil', name: 'Connecte')]
    public function profilParticipantConnecte(
        Request $request,
        EntityManagerInterface $entityManager) : Response
    {

        $participant = $this->getUser();

        $participantForm = $this->createForm(ProfilParticipantConnecteType::class, $participant );
        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted() && $participantForm->isValid()){

            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Profil modifié avec succès!');
        }

        return $this->render('profil/profilParticipantConnecte.html.twig', [
            'participantForm' => $participantForm->createView()
        ]);
    }
    #[Route('/monProfil/reset', name: 'resetPassword')]
    public function resetPasswordParticipant(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher) : Response
    {
        $participant = $this->getUser();
        $passwordForm = $this->createForm(ResetPasswordType::class, $participant);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()){
            $motPasse= $passwordForm->get('motPasse')->getData();
            $confirmation= $passwordForm->get('confirmation')->getData();

            if($motPasse===$confirmation){

                $motPassHash= $passwordHasher->hashPassword(
                    $participant,
                    $motPasse
                );

                $participant->setMotPasse($motPassHash);

                $entityManager->persist($participant);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe modifié avec succès !');

                $entityManager->refresh($participant);

            }
            else{
                $this->addFlash('error', 'Erreur dans la double saisie');

                //refresh permet d'éviter la déco en cas de mismatch entre le mdp et la confirmation
                $entityManager->refresh($participant);
            }
        }


        return $this->render('reset_password/resetPassword.html.twig', [
                'passwordForm'=>$passwordForm->createView()
        ]);
    }
}
