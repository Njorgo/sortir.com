<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Participant;
use App\Entity\Ville;
use App\Entity\Lieu;
use App\Form\CreerSortieType;
use App\Form\CreerLieuType;
use App\Repository\SortieRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/sortie', name:'sortie_')]
class SortieController extends AbstractController
{
    //traitement du formulaire de création de sortie
    #[Route('/creer', name: 'creer')]
    public function creer(
        Request $request,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepositery,
        ParticipantRepository $participantRepository,
        LieuRepository $lieuRepository,
        CreerSortieType $creerSortieType,
        Security $security,
        $sortieId = null
    ): Response {
        $sortie = new Sortie();

        if (!$sortieId) {
            $sortie = new Sortie();
        } else {
            $sortie = $sortieRepository->find($sortieId);
        }
        $creerSortieForm = $this->createForm(CreerSortieType::class, $sortie);

        $creerSortieForm->handleRequest($request);

        $lieu = new Lieu();

        if ($creerSortieForm->get('Sauvegarder')->isClicked() && $creerSortieForm->isValid()) {

            $creerLieu = $creerSortieForm->get('creerLieu')->getData();
            if ($creerLieu) {
                $sortie->setLieuSortie($creerLieu);
            }

            if ($creerSortieForm->isValid()) {

                $etat = $etatRepositery->findOneBy(["libelle" => "Créée"]);
                $sortie->setEtat($etat);
                $sortie->setOrganisateur($this->getUser());
                $sortie->setSiteOrganisateur($this->getUser()->getCampus());
                $entityManager->persist($sortie);
                $sortie->addInscrit($this->getUser());
                $entityManager->flush();

                $this->addFlash('success', 'La sortie a bien été créée');
                return $this->redirectToRoute('main_home');
            }
        }

        if ($creerSortieForm->get('Publier')->isClicked() && $creerSortieForm->isSubmitted()) {

            $creerLieu = $creerSortieForm->get('creerLieu')->getData();
            if ($creerLieu) {
                $sortie->setLieuSortie($creerLieu);
            }

            if ($creerSortieForm->isValid()) {
                $etat = $etatRepositery->findOneBy(["libelle" => "Ouverte"]);
                $sortie->setEtat($etat);
                $sortie->setOrganisateur($this->getUser());
                $sortie->setSiteOrganisateur($this->getUser()->getCampus());
                $entityManager->persist($sortie);
                $sortie->addInscrit($this->getUser());
                $entityManager->flush();

                $this->addFlash('success', 'La sortie a bien été publiée');
                return $this->redirectToRoute('main_home');
            }
        }

        return $this->render('sortie/creer.html.twig', [
            'creerSortieForm' => $creerSortieForm->createView()
        ]);
    }

    #[Route('/afficher/{sortieId}')]
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
