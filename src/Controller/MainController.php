<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Filtre\FiltreClass;
use App\Entity\Sortie;
use App\Form\FiltreType;
use App\Repository\SortieRepository;
use App\Service\MajEtat;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController {

    #[Route('/', name: 'main_home')]
    public function affichageDonnees(
        SortieRepository $sortieRepository,
        Request $request,
        MajEtat $majEtat) : Response
    {
        //Mise à jour de l'état des sorties en place dans la BDD
        $majEtat->majEtat();

        //gestion des filtres via formulaire
        $data= new FiltreClass();
        $data->campus=$this->getUser()->getCampus();

        $formFiltre = $this->createForm(FiltreType::class, $data);
        $formFiltre->handleRequest($request);

        $listeInfosSortie = $sortieRepository->listeInfosSorties($data, $this->getUser());        

        return $this->render('main/home.html.twig', [
            'listeInfosSortie' => $listeInfosSortie,
            'formFiltre'=> $formFiltre->createView()

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
    )
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
        EntityManagerInterface $entityManagerInterface)
    {

        $sortie_id->removeInscrit($participant_id);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('main_home');

    }  
}