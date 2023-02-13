<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Participant;
use App\Form\CreerSortieType;
use App\Repository\SortieRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    //traitement du formulaire de création de sortie
    #[Route('/creer', name: 'creer')]
    public function creer(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatRepository $etatRepositery,
     ParticipantRepository $participantRepository, LieuRepository $lieuRepository, CreerSortieType $creerSortieType, $sortieId=null): Response
    { 
        $sortie = new Sortie();

        if (!$sortieId) {
            $sortie = new Sortie();
        } else {
            $sortie = $sortieRepository->find($sortieId);
        }
        $creerSortieForm = $this->createForm(CreerSortieType::class, $sortie);

        $creerSortieForm->handleRequest($request);

        if ($creerSortieForm->isSubmitted() && $creerSortieForm->isValid()) {
            $sortie = $creerSortieForm->getData();
            if ($creerSortieForm->get("Sauvegarder")->isClicked()) {
                $sortie->setEtat($etatRepositery->findOneBy(["libelle" => "Créée"]));
            } elseif ($creerSortieForm->get("Publier")->isClicked()) {
                $sortie->setEtat($etatRepositery->findOneBy(["libelle" => "Ouverte"]));
            } else {
                $sortieRepository->remove($sortieRepository->find($sortieId));
                $entityManager->flush();
                return $this->redirectToRoute('main_home');
            }


            $sortie->setOrganisateur($this->getUser());
            $sortie->setSiteOrganisateur($this->getUser()->getCampus());
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été publiée');
            return $this->redirectToRoute('main_home');
        }

        return $this->render('sortie/creer.html.twig', [
            'creerSortieForm' => $creerSortieForm->createView()
        ]);
    }

    #[Route('/detailSortie/{sortieId}')]
    public function detailSortie(int $sortieId, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($sortieId);

        if (!$sortie) {
            throw $this->createNotFoundException('Oups, petite erreur 404 :)');
        }

        return $this->render('sortie/detailSortie.html.twig', [
            'sortie' => $sortie
        ]);
    }
}
