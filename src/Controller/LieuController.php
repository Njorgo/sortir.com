<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\CreeLieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Message;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Routing\Annotation\Route;

 #[Route('/lieu', name:'lieu_')]
class LieuController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(LieuRepository $lieuRepository): Response
    {
        return $this->render('lieu/index.html.twig', [
            'lieux' => $lieuRepository->findAll(),
        ]);
    }

    #[Route('/cree', name:'cree', methods: ['GET', 'POST'])]    
    public function cree(Request $request,EntityManagerInterface $entityManager,LieuRepository $lieuRepository,Notification $notification): Response
    {


        $lieu = new Lieu();
        $creerLieuForm=$this->createForm(CreeLieuType::class,$lieu );
        $creerLieuForm->handleRequest($request);        

        if($creerLieuForm->isSubmitted() && $creerLieuForm->isValid()){            
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('success', 'Votre lieu est bien enregistré !');

            return  $this->redirectToRoute('sortie_creer', [], Response::HTTP_SEE_OTHER);

    }
        return $this->render('lieu/ajouter.html.twig', [
            'Lieu' => $lieu,
            'CreerLieuForm' =>  $creerLieuForm,
                ]);
            }
}