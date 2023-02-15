<?php

namespace App\Controller\Api;

use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiLieuController extends AbstractController
{
    
    #[Route('/api/lieux', name: 'api_lieux_liste', methods: ['GET'])]    
    public function liste(Request $request, LieuRepository $lieuRepository)
    {
        $villeId = $request->query->get("id");
        $lieux = $lieuRepository->lieuParVille($villeId);
        return $this->json($lieux, Response::HTTP_OK, [], ['groups' => 'liste_lieux']);
    }


}