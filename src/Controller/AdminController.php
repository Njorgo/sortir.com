<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CreerParticipantType;
use App\Repository\CampusRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/villes', name: 'admin_villes')]
    public function villes(VilleRepository $villeRepository): Response
    {
        return $this->render('admin/villes.html.twig', [
            'villes' => $villeRepository->findAll()
        ]);
    }

    #[Route('/admin/campus', name: 'admin_campus')]
    public function campus(CampusRepository $campusRepository): Response
    {
        return $this->render('admin/campus.html.twig', [
            'campus' => $campusRepository->findAll(),
        ]);
    }

    #[Route('/admin/participant', name: 'admin_participant')]
    public function creerParticipant(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();
        $creerParticipantForm = $this->createForm(CreerParticipantType::class, $user);
        $creerParticipantForm->handleRequest($request);

        if ($creerParticipantForm->isSubmitted() && $creerParticipantForm->isValid()) {
            // encode the plain password
            $user->setMotPasse($userPasswordHasher->hashPassword($user,$creerParticipantForm->get('motPasse')->getData())
            );
            $user->setActif(1);


            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $this->addFlash('success', 'L\'utilisateur a bien été créée');
            return $this->redirectToRoute('main_home');
        }

        return $this->render('admin/creerPartcipant.html.twig', [
            'creerParticipantForm' => $creerParticipantForm->createView(),
        ]);
    }
}