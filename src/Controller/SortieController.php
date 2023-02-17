<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnulationSortieType;
use App\Form\CreerSortieType;
use App\Repository\SortieRepository;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
                $this->addFlash('success', 'La sortie a bien été supprimée');
            }
    
            return $this->redirectToRoute('main_home');
        }
    
        #[Route('sortie/annuler/{sortieId}', name: 'sortie_annuler_admin')]
        public function annuler($sortieId,
                                SortieRepository $sortieRepository,
                                Request $request,
                                EntityManagerInterface $entityManager,
                                EtatRepository $etatRepository): Response
        {
            $etatAnnulee = $etatRepository->findOneBy(['libelle'=> 'Annulée']);
            $sortie = $sortieRepository->findOneBy(['id' => $sortieId], []);

            $annulationForm= $this->createForm(AnnulationSortieType::class);
            $annulationForm->handleRequest($request);

            if ($annulationForm->isSubmitted() && $annulationForm->isValid() ){
                $motif = $annulationForm->get('infosSortie')->getData();
                $sortie->setInfosSortie($motif);
                $sortie->setEtat($etatAnnulee);
                $entityManager->persist($sortie);
                $entityManager->flush();
            $this->addFlash('success', 'La sortie a bien été annulée');
            return $this->redirectToRoute('main_home');

            }



            return $this->render('sortie/annuler.html.twig', [    
                'sortie' => $sortie,
                'annulationForm'=> $annulationForm->createView()
            ]);
    
        }

    #[Route('sortie/afficher/{sortieId}', name: 'sortie_details')]
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


    #[Route('sortie/publier/{sortieId}')]
    public function chgtEtatCreeeEnOuverte(
        Sortie $sortieId,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository): Response
    {
        $sortie = $sortieRepository->findOneBy(['id' => $sortieId], []);
        $newEtat = $etatRepository->findOneBy(["libelle" => "Ouverte"]);
        $sortieRepository->changerEtat($newEtat, $sortie);

        return $this->redirectToRoute('main_home');
    }

    #[Route('sortie/modifier/{sortieId}', name: 'sortie_modifier')]
    public function modifier(
        $sortieId,
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager,
        Request $request): Response
    {
        $sortie = $sortieRepository->findOneBy(['id'=> $sortieId]);
        $lieu = $sortie->getLieuSortie();
        dump($lieu);
        $modificationForm= $this->createForm(CreerSortieType::class, $sortie);
        $modificationForm->handleRequest($request);

        if ($modificationForm->isSubmitted() && $modificationForm->isValid()  ){
            $modification = $modificationForm->getData();
            $entityManager->persist($modification);
            $entityManager->flush();
            $this->addFlash('success', 'La sortie a bien été modifiée');
            $this->redirectToRoute('main_home');
        }


        return $this->render('sortie/modificationSortie.html.twig',[
            'sortie'=> $sortie,
            'modificationForm'=> $modificationForm->createView()
        ]);

    }

    /**
     * @param Sortie $sortie_id
     * @param Participant $participant_id
     *
     * @Route("/inscrire-sortie/{sortie_id}/{participant_id}", requirements={"'sortie_id" = "\d+", "participant_id" = "\d+" }, name="inscrire_sortie")
     * @return RedirectResponse
     *
     */
    public function inscrireSortie(
        Sortie $sortie_id,
        Participant $participant_id,
        EntityManagerInterface $entityManagerInterface,
    ): RedirectResponse
    {

        $sortieInscription = $sortie_id->addInscrit($participant_id);
        $entityManagerInterface->persist($sortieInscription);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('main_home');

    }

    /**
     * @param Sortie $sortie_id
     * @param Participant $participant_id
     *
     * @Route("/desistement-sortie/{sortie_id}/{participant_id}", requirements={"'sortie_id" = "\d+", "participant_id" = "\d+" }, name="desistement_sortie")
     * @return RedirectResponse
     *
     */
    public function desistementSortie(
        Sortie $sortie_id,
        Participant $participant_id,
        EntityManagerInterface $entityManagerInterface): RedirectResponse
    {

        $sortie_id->removeInscrit($participant_id);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('main_home');

    }
}
