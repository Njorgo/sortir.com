<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreerSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    //traitement du formulaire de création de sortie
    #[Route('/creer', name: 'creer')]
    public function creer(Request $request, EntityManagerInterface $entityManager): Response
    { // TODO mettre en place setEtat, setOrganisateur et setSiteOrganisateur
        $sortie = new Sortie();
        $creerSortieForm = $this-> createForm(CreerSortieType::class, $sortie);

        $creerSortieForm->handleRequest($request);

        if ($creerSortieForm->isSubmitted()) {
            $entityManager->persist($creerSortieForm);
            $entityManager->flush();

            $this->addFlash('success','La sortie a bien été publiée');
            return $this->redirectToRoute('main_home');
        }

        return $this->render('sortie/creer.html.twig', [
            'creerSortieForm'=> $creerSortieForm->createView()
        ]);
    }
}
