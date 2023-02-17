<?php

namespace App\Controller;

use App\Filtre\FiltreClass;
use App\Form\FiltreType;
use App\Repository\SortieRepository;
use App\Service\MajEtat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

}