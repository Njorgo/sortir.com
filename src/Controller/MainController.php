<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Filtre\FiltreClass;
use App\Entity\Sortie;
use App\Form\FiltreType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Service\GestionEtatSortie;
use DateInterval;
use DateTime;
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
        Request $request) : Response
    {
        //gestion des filtres via formulaire
        $data= new FiltreClass();
        $data->campus=$this->getUser()->getCampus();

        $formFiltre = $this->createForm(FiltreType::class, $data);
        $formFiltre->handleRequest($request);

        $listeInfosSortie = $sortieRepository->listeInfosSorties($data, $this->getUser());

        /* Création du tableau des actions*/
        /*$etatAction = [
            1 => 'Afficher',
            2 => 'Se désister' ,
            3 => "S'inscrire",
            4 => 'Modifier',
            5 =>'Publier',
            6 =>'Annuler'
        ];*/

        /* Affichage des informations demandés pour les différentes sorties proposées */

        /*$listeInfosSortieRender = [];
        $dateHeureActuelle = new DateTime("now");


        /*$participant = $this->getUser();
        foreach($listeInfosSortie as $infosSortie) {
            $dureeInterval = DateInterval::createFromDateString(strval($infosSortie['duree']). ' min');
            $dateHeureFin = clone $infosSortie['dateHeureDebut'];
            date_add($dateHeureFin, $dureeInterval);
            if ($dateHeureActuelle >= $infosSortie['dateHeureDebut'] AND $dateHeureActuelle < $dateHeureFin) {
                $infosSortie['libelle'] = 'Activité en cours';
                $infosSortie['action'] = 'Afficher';
                $infosSortie['action2'] = '';
            } elseif ($dateHeureActuelle > $infosSortie['dateHeureDebut']) {
                $infosSortie['libelle'] = 'Passée';
                $infosSortie['action'] = 'Afficher';
                $infosSortie['action2'] = '';

            } else {
                $infosSortie['libelle'] = 'Ouverte';
                $infosSortie['action'] = 'Afficher';
            }
            if($infosSortie['organisateurId'] == $participant->getId() AND $dateHeureActuelle < $infosSortie['dateHeureDebut']){
                $infosSortie['action2'] = ' - Annuler';
            }
            /* if($infosSortie['organisateurId'] == $participant->getId() AND $infosSortie['organisateurId'] == $infosSortie['sortie_ID']){
                 $infosSortie['action'].= ' - Se désister';
             }*/
            /*  if($infosSortie['organisateurId'] == $participant->getId() AND $participant->getInscrits() !== null) {
                  $infosSortie['inscrit'] = 'X';
                  $infosSortie['action'] .= ' - Se désister';
              }*/
            /*elseif($dateHeureActuelle < $infosSortie['dateHeureDebut']){
                $infosSortie['action2'] = " - S'inscrire";
            }
            array_push($listeInfosSortieRender, $infosSortie);
        }*/

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

    #[Route('/supprimer/{idSortie}', name: 'supprimer')]
    public function supprimer($sortieId, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, CampusRepository $campusRepository,
    ParticipantRepository $participantRepository, GestionEtatSortie $gestionEtatSortie,): Response {
        $sortie = $sortieRepository->findOneBy(['id' => $sortieId], []);
        $erreur = false;
        $user = $participantRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

        if ($sortie->getEtat() != "Créée") {
            $this->addFlash('error', "Vous ne pouvez pas modifier une sortie qui n'est créée");
            $erreur = true;
        }
        if ($sortie->getOrganisateur() !== $user) {
            $this->addFlash('error', "Vous ne pouvez pas modifier une sortie que vous n'organisée pas");
            $erreur = true;
        }

        if ($erreur == false) {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }
        $gestionEtatSortie->verifierEtat();
        $campus = $campusRepository->findAll();
        $sorties = $sortieRepository->findAll();

        return $this->render('home.html.twig', [
            "sorties" => $sorties,
            "campus" => $campus,
        ]);
    }


}