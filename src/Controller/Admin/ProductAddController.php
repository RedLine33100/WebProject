<?php

namespace App\Controller\Admin;

use App\Entity\Account;
use App\Entity\Pays;
use App\Entity\Produit;
use App\Form\ProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductAddController extends AbstractController
{
    #[Route('/admin/addproduct', name: 'app_admin_addproduct')]
    public function index(#[CurrentUser] Account $account, ValidatorInterface $validator, Request $request, EntityManagerInterface $entityManager): Response
    {

        if($account->getAccountType() != 1){
            return $this->redirectToRoute('app_produits_p');
        }

        $form = $this->createForm(ProductFormType::class);

        $form->handleRequest($request);
        if($form->isSubmitted()){

            $newProduct = new Produit();
            $newProduct->setName($form->get('name')->getData());
            $newProduct->setPrice($form->get('price')->getData());
            $newProduct->setDescription($form->get('description')->getData());
            $newProduct->setNumber($form->get('number')->getData());

            foreach ($form->get('pays')->getData() as $pays){

                $paysEntity = $entityManager->getRepository(Pays::class)->findOneBy(["id"=>$pays]);
                if($paysEntity == null)
                    continue;
                $newProduct->addPay($paysEntity);

            }

            $constraintViolation = $validator->validate($newProduct);
            if($constraintViolation->count() != 0){

                $message = "";
                $cntError = 1;

                foreach ($constraintViolation as $violation){
                    $message = $message . $cntError . ": " . $violation->getMessage() . "<br>";
                    $cntError++;
                }

                $this->addFlash("error", $message);
                return $this->redirectToRoute('app_admin_addproduct');

            }

            $entityManager->persist($newProduct);
            $entityManager->flush();

            $this->addFlash('win', 'Produit ajoute');

            return $this->redirectToRoute('app_produits_p');

        }else{
            $this->addFlash("error", "none");
        }

        return $this->render('mod/productadd.html.twig', [
            'controller_name' => 'ProductAddController',
            'createForm'=>$form->createView()
        ]);

    }
}
