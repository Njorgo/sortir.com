<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Ville;
use App\Form\CampusFiltreType;
use App\Form\CampusType;
use App\Form\CreerParticipantType;
use App\Form\VilleFiltreType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/villes', name: 'admin_villes')]
    public function villes(Request $request, EntityManagerInterface $em, VilleRepository $villeRepository)
    {
        $villeFiltreForm = $this->createForm(VilleFiltreType::class);

        if($villeFiltreForm->handleRequest($request)->isSubmitted() && $villeFiltreForm->isValid()) {
            $value = $villeFiltreForm['nom']->getData();

            $resultat = $villeRepository->findByVilles($value);
        }
        else {
            $resultat = $em->getRepository(Ville::class)->findAll();
        }

        return $this->render('admin/villes.html.twig', [
            'villes' => $resultat,
            'villeFiltreForm' => $villeFiltreForm->createView()
        ]);
    }


    #[Route('/admin/villes/ajouter', name: 'admin_villes_ajouter')]
    public function ajouterVille(Request $request, EntityManagerInterface $em){
        $ville = new Ville();
        $ajouterVilleForm = $this->createForm(VilleType::class, $ville);
        $ajouterVilleForm -> handleRequest($request);

        if ($ajouterVilleForm->isSubmitted() && $ajouterVilleForm->isValid()) {
            $ville = $ajouterVilleForm->getData();
            $em->persist($ville);
            $em->flush();
            $this->addFlash('success', 'La ville a bien été ajoutée');
            return $this->redirectToRoute('admin_villes');
        }

        return $this->render('admin/ajouterVille.html.twig', [
            'ajouterVilleForm' => $ajouterVilleForm->createView()
        ]);
    }

    #[Route('/admin/villes/modifier/{id}', name: 'admin_villes_modifier')]
    public function modifierVille(Ville $ville, Request $request, EntityManagerInterface $em)
    {
        $modifierVilleForm = $this->createForm(VilleType::class, $ville);        
    
        $modifierVilleForm->handleRequest($request);

        if($modifierVilleForm->isSubmitted() && $modifierVilleForm->isValid()){
            $ville = $modifierVilleForm->getData();

            $em->persist($ville);
            $em->flush();
            $this->addFlash('success', 'La ville a bien été modifiée');

            return $this->redirectToRoute('admin_villes');
        }

        return $this->render('admin/modifierVille.html.twig', [
            'modifierVilleForm' => $modifierVilleForm->createView()
        ]);
    }

    #[Route('/admin/villes/supprimer/{id}', name: 'admin_villes_supprimer')]
    public function supprimerVille(Ville $ville, Request $request, EntityManagerInterface $em)
    {
        $ville = $em->getRepository(Ville::class)->find($request->get('id'));

        $em->remove($ville);
        $em->flush();
        $this->addFlash('success', 'La ville a été supprimée');

        return $this->redirectToRoute('admin_villes');
    }


    #[Route('/admin/campus', name: 'admin_campus')]
    public function campus(Request $request, EntityManagerInterface $em, CampusRepository $campusRepository)
    {
        $campusFiltreForm = $this->createForm(CampusFiltreType::class);

        if($campusFiltreForm->handleRequest($request)->isSubmitted() && $campusFiltreForm->isValid()) {
            $value = $campusFiltreForm['nom']->getData();

            $resultat = $campusRepository->findByCampus($value);
        }
        else {
            $resultat = $em->getRepository(Campus::class)->findAll();
        }

        return $this->render('admin/campus.html.twig', [
            'campus' => $resultat,
            'campusFiltreForm' => $campusFiltreForm->createView()
        ]);
    }

    #[Route('/admin/campus/creer', name: 'admin_campus_creer')]
    public function creerCampus(Request $request, EntityManagerInterface $em)
    {
        $campus = new Campus();
        $creerCampusForm = $this->createForm(CampusType::class, $campus);
        $creerCampusForm->handleRequest($request);

        if ($creerCampusForm->isSubmitted() && $creerCampusForm->isValid()) {
            $campus = $creerCampusForm->getData();
            $em->persist($campus);
            $em->flush();
            $this->addFlash('success', 'Le campus a bien été ajouté');
            return $this->redirectToRoute('admin_campus');
        }

        return $this->render('admin/creerCampus.html.twig', [
            'creerCampusForm' => $creerCampusForm->createView()
        ]);
    }

    #[Route('/admin/campus/modifier/{id}', name: 'admin_campus_modifier')]
    public function modifierCampus(campus $campus, Request $request, EntityManagerInterface $em)
    {
        $modifierCampusForm = $this->createForm(CampusType::class, $campus);

        $modifierCampusForm->handleRequest($request);

        if ($modifierCampusForm->isSubmitted() && $modifierCampusForm->isValid()) {
            $campus = $modifierCampusForm->getData();

            $em->persist($campus);
            $em->flush();
            $this->addFlash('success', 'Le campus a bien été modifé');

            return $this->redirectToRoute('admin_campus');
        }

        return $this->render('admin/modifierCampus.html.twig', [
            'modifierCampusForm' => $modifierCampusForm->createView()
        ]);
    }

    #[Route('/admin/campus/supprimer/{id}', name: 'admin_campus_supprimer')]
    public function supprimerCampus(Request $request, Campus $campus, EntityManagerInterface $em): Response
    {
        $campus = $em->getRepository(campus::class)->find($request->get('id'));

        $em->remove($campus);
        $em->flush();
        $this->addFlash('success', 'Le campus a bien été supprimé');

        return $this->redirectToRoute('admin_campus', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/participant', name: 'admin_participant')]
    public function creerParticipant(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();
        $creerParticipantForm = $this->createForm(CreerParticipantType::class, $user);
        $creerParticipantForm->handleRequest($request);

        if ($creerParticipantForm->isSubmitted() && $creerParticipantForm->isValid()) {
            
            $user->setMotPasse(
                $userPasswordHasher->hashPassword($user, $creerParticipantForm->get('motPasse')->getData())
            );
            $user->setActif(1);


            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'utilisateur a bien été créée');
            return $this->redirectToRoute('main_home');
        }

        return $this->render('admin/creerPartcipant.html.twig', [
            'creerParticipantForm' => $creerParticipantForm->createView(),
        ]);
    }
}
