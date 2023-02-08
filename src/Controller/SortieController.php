<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreerSortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/creer', name: 'creer')]
    public function creer(Request $request): Response
    {
        $sortie = new Sortie();
        $creerSortieForm = $this-> createForm(CreerSortieType::class, $sortie);


        return $this->render('sortie/creer.html.twig', [
            'creerSortieForm'=> $creerSortieForm->createView()
        ]);
    }
}
