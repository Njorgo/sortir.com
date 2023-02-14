<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreerSortieType;
use App\Repository\SortieRepository;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class SortieController extends AbstractController
{
    //traitement du formulaire de création de sortie
    #[Route('/sortie/creer', name: 'sortie_creer')]
    public function creer(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepositery): Response {
        
        $sortie = new Sortie();
        $creerSortieForm = $this->createForm(CreerSortieType::class, $sortie);
        $creerSortieForm->handleRequest($request);
        
        if ($creerSortieForm->isSubmitted() && $creerSortieForm->isValid()) {
            if ($creerSortieForm->get('Publier')->isClicked()) {
                $etat = $etatRepositery->findOneBy(["libelle" => "Ouverte"]);
            }else{
                $etat = $etatRepositery->findOneBy(["libelle" => "Créée"]); 
            }
            $sortie->setEtat($etat);
            $sortie->setOrganisateur($this->getUser());
            $sortie->setSiteOrganisateur($this->getUser()->getCampus());
            $entityManager->persist($sortie);
            $sortie->addInscrit($this->getUser());
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été créée');
            return $this->redirectToRoute('main_home');
        }    

            return $this->render('sortie/creer.html.twig', [
            'creerSortieForm' => $creerSortieForm -> createView(),
        ]);            
        }

        #[Route('sortie/supprimer/{sortieId}', name: 'sortie_supprimer', methods: ['POST'])]
        public function delete(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
        {
            if ($this->isCsrfTokenValid('supprimer' . $sortie->getId(), $request->request->get('_token'))) {
                $entityManager->remove($sortie);
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('main_home');
        }
    
        #[Route('sortie/annuler/{sortieId}', name: 'sortie_annuler_admin')]
        public function annuler($sortieId, SortieRepository $sortieRepository): Response
        {
            $sortie = $sortieRepository->findOneBy(['id' => $sortieId], []);
            $annuler = true;
            return $this->render('sortie/annuler.html.twig', [    
                'sortie' => $sortie,
                'annuler' => $annuler,
            ]);
    
        }

    #[Route('sortie/afficher/{sortieId}')]
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
