<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\CreerLieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LieuController extends AbstractController
{
    #[Route('/lieu', name: 'lieu_index', methods: ['GET'])]
    public function index(LieuRepository $lieuRepository): Response
    {
        return $this->render('lieu/index.html.twig', [
            'lieux' => $lieuRepository->findAll(),
        ]);
    }

    #[Route('/lieu/cree', name:'lieu_cree', methods: ['GET', 'POST'])]    
    public function cree(Request $request,EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $creerLieuForm = $this->createForm(CreerLieuType::class, $lieu );
        $creerLieuForm->handleRequest($request);        

        if($creerLieuForm->isSubmitted() && $creerLieuForm->isValid()){            
            $entityManager->persist($lieu);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre lieu est bien enregistré !');
            return  $this->redirectToRoute('sortie_creer');

    }
        return $this->render('lieu/ajouter.html.twig', [
            'Lieu' => $lieu,
            'creerLieuForm' =>  $creerLieuForm->createView()
                ]);
            }
}