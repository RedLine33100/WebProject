<?php

namespace App\Controller\Admin;

use App\Entity\Account;
use App\Entity\Pays;
use App\Entity\Produit;
use App\Form\ProductFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductModifyController extends AbstractController
{

    public function acceptForm(ValidatorInterface $validator, FormInterface $form, EntityManagerInterface $entityManager, int $productID)
    {

        $product = $entityManager->getRepository(Produit::class)->findOneBy(["id"=>$productID]);

        if($product == null){
            $this->addFlash("error", "Pas de produit");
            return;
        }

        $product->getPays()->clear();

        foreach ($form->get('pays')->getData() as $paysID){

            $pays = $entityManager->getRepository(Pays::class)->findOneBy(["id"=>$paysID]);

            if($pays == null){
                $this->addFlash("error", "Pas de pays");
                return;
            }

            $product->getPays()->add($pays);

        }

        $product->setNumber($form->get('number')->getData());
        $product->setName($form->get('name')->getData());
        $product->setDescription($form->get('description')->getData());
        $product->setPrice($form->get('price')->getData());

        $constraintViolation = $validator->validate($product);
        if($constraintViolation->count() != 0){

            $message = "";
            $cntError = 1;

            foreach ($constraintViolation as $violation){
                $message = $message . $cntError . ": " . $violation->getMessage() . "<br>";
                $cntError++;
            }

            $this->addFlash("error", $message);
            return;

        }

        $this->addFlash("win", "Produit modifié");

        $entityManager->persist($product);
        $entityManager->flush();

    }

    #[Route('/product/modify/{id}', name: 'app_product_modify')]
    public function index(#[CurrentUser] Account $account, ValidatorInterface $validator, Request $request, EntityManagerInterface $entityManager, int $id): Response
    {

        if($account->getAccountType() != 1){
            return $this->redirectToRoute('app_produits_p');
        }

        $product = $entityManager->getRepository(Produit::class)->findOneBy(["id"=>$id]);

        if($product == null) {

            $this->addFlash("error", "Aucun article trouvé");
            return $this->render('mod/modifyproduct.html.twig', [
                'controller_name' => 'ProductModifyController',
            ]);

        }

        $form = $this->createForm(ProductFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->acceptForm($validator, $form, $entityManager, $id);
            return $this->redirectToRoute('app_product_modify', ["id"=>$id]);

        }

        if($form->get('name')->getData() == null){
            $form->get('name')->setData($product->getName());
        }

        if($form->get('description')->getData() == null){
            $form->get('description')->setData($product->getDescription());
        }

        if($form->get('price')->getData() == null){
            $form->get('price')->setData($product->getPrice());
        }

        if($form->get('number')->getData() == null){
            $form->get('number')->setData($product->getNumber());
        }

        if($form->get('pays')->getData() != null){
            $collection = new ArrayCollection();
            foreach ($product->getPays() as $pays){
                $collection->add($pays->getId());
            }
            $form->get('pays')->setData($collection);
        }

        return $this->render('mod/modifyproduct.html.twig', [
            'controller_name' => 'ProductModifyController',
            'updateForm'=>$form
        ]);

    }
}
