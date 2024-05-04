<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produits', name: 'app_produits')]
class ProduitsController extends AbstractController
{
    #[Route('/', name: '_p')]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produits/index.html.twig', [
            'controller_name' => 'ProduitsController',
            'products' => $produitRepository->findAll()
        ]);
    }
}
