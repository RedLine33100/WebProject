<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Cart;
use App\Entity\ProduitCart;
use App\Form\ProductCartClearFormType;
use App\Form\ProductCartPaidFormType;
use App\Form\ProductCartRemoveFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class CartController extends AbstractController
{

    #[Route('/cart', name: 'app_cart')]
    public function index(#[CurrentUser] Account $account, Request $request, EntityManagerInterface $entityManager): Response
    {

        $cart = $entityManager->getRepository(Cart::class)->findOneBy(["account"=>$account->getId(), "isPaid"=>false]);

        if($cart == null){
            $message = "new cart";
            $cart = new Cart();
            $cart->setPaid(false);
            $cart->setAccount($account);
            $entityManager->persist($cart);
            $entityManager->flush();
        }else{
            $message = "POP: ".$cart->getItems()->count();
        }

        $suppFormCheck = $this->createForm(ProductCartRemoveFormType::class);
        $suppFormCheck->handleRequest($request);

        if($suppFormCheck->isSubmitted() && $suppFormCheck->isValid()){

            $pCartID = $suppFormCheck->get('pID')->getData();
            $pCart = $entityManager->getRepository(ProduitCart::class)->findOneBy(["id"=>$pCartID]);

            if($pCart != null){
                $pCart->getProduit()->setNumber($pCart->getProduit()->getNumber()+$pCart->getAmount());
                $cart->removeItem($pCart);
                $entityManager->remove($pCart);
                $entityManager->persist($cart);
                $entityManager->persist($pCart->getProduit());
                $entityManager->flush();


                $this->addFlash('win', 'Produit retiré');
                return $this->redirectToRoute('app_cart');

            }else {


                $this->addFlash('error', 'Le produit n\'est pas dans le cart');
                return $this->redirectToRoute('app_welcome');

            }

        }

        $suppForms = [];
        $cnt = 0;
        foreach ($cart->getItems() as $pCart){

            $newForm = $this->createForm(ProductCartRemoveFormType::class);
            $newForm->get('pID')->setData($pCart->getId());
            $suppForms[$cnt] = $newForm->createView();
            $cnt++;

        }

        $paidForm = $this->createForm(ProductCartPaidFormType::class);
        $paidForm->handleRequest($request);

        if($paidForm->isSubmitted()){
            $cart->setPaid(true);
            $entityManager->persist($cart);
            $entityManager->flush();

            $this->addFlash('win', 'Payé');

            return $this->redirectToRoute('app_cart');
        }

        $clearForm = $this->createForm(ProductCartClearFormType::class);
        $clearForm->handleRequest($request);

        if($clearForm->isSubmitted()){

            foreach($cart->getItems() as $cartItem){
                $cartItem->getProduit()->setNumber($cartItem->getProduit()->getNumber()+$cartItem->getAmount());
                $entityManager->persist($cartItem->getProduit());
            }

            $entityManager->remove($cart);
            $entityManager->flush();
            $this->addFlash('win', 'Nettoyé');

            return $this->redirectToRoute('app_cart');

        }

        if($cart->getItems()->count() != 0) {

            $prixTotal = 0;
            foreach($cart->getItems() as $cartItem){
                $prixTotal = $prixTotal+($cartItem->getAmount()*$cartItem->getProduit()->getPrice());
            }

            return $this->render('cart/index.html.twig', [
                'controller_name' => 'CartController',
                'cartProducts' => $cart->getItems(),
                'message' => $message,
                'suppForms' => $suppForms,
                'paidButton' => $paidForm->createView(),
                'clearButton' => $clearForm->createView(),
                'prixTotal' => $prixTotal
            ]);

        }else{
            return $this->render('cart/index.html.twig', [
                'controller_name' => 'CartController',
                'cartProducts' => $cart->getItems(),
                'message' => $message,
                'suppForms' => $suppForms
            ]);
        }
    }
}
