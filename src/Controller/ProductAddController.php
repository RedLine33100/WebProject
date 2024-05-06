<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Pays;
use App\Entity\Produit;
use App\Form\ProductAddFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ProductAddController extends AbstractController
{
    #[Route('/produits/add', name: 'app_produits_add')]
    public function index(#[CurrentUser] Account $account, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(ProductAddFormType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $newProduct = new Produit();
            $newProduct->setName($form->get('name')->getData());
            $newProduct->setPrice($form->get('price')->getData());
            $newProduct->setDescription($form->get('description')->getData());

            foreach ($form->get('pays')->getData() as $pays){

                $paysEntity = $entityManager->getRepository(Pays::class)->findOneBy(["id"=>$pays]);
                if($paysEntity == null)
                    continue;
                $newProduct->addPay($paysEntity);

            }

            $entityManager->persist($newProduct);
            $entityManager->flush();

            return $this->redirectToRoute('app_produits_p');

        }

        return $this->render('product_add/index.html.twig', [
            'controller_name' => 'ProductAddController',
            'createForm'=>$form->createView()
        ]);

    }
}
