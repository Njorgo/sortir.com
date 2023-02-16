<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/lieux', name: 'api_lieux_liste', methods: ['GET'])]
    public function liste(int $id, VilleRepository $villesRepository): Response
    {
        $ville = $villesRepository->find($id);
        $lieux = ($ville == null) ? [] : $ville->getLieux();


        return $this->json($lieux, Response::HTTP_OK, [], ['groups' => 'list_lieux']);
    }
}
