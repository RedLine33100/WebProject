<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Cart;
use App\Entity\ProduitCart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class CartController extends AbstractController
{

    #[Route('/cart/drop/{id}', name: 'app_cart_drop_id')]
    public function dropItem(#[CurrentUser] Account $account, EntityManagerInterface $entityManager, int $id){

        $cart = $entityManager->getRepository(Cart::class)->findOneBy(["account"=>$account->getId(), "isPaid"=>false]);

        if($cart == null){
            $this->addFlash("error", "Impossible, pas de cart");
            return $this->redirectToRoute('app_cart');
        }

        $pCart = $entityManager->getRepository(ProduitCart::class)->findOneBy(["id"=>$id]);

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
            return $this->redirectToRoute('app_cart');

        }
    }

    #[Route('/cart/validate', name: 'app_cart_validate')]
    public function validateCart(#[CurrentUser] Account $account, EntityManagerInterface $entityManager){

        $cart = $entityManager->getRepository(Cart::class)->findOneBy(["account"=>$account->getId(), "isPaid"=>false]);

        if($cart == null){
            $this->addFlash("error", "Impossible, pas de cart");
            return $this->redirectToRoute('app_cart');
        }

        $cart->setPaid(true);
        $entityManager->persist($cart);
        $entityManager->flush();

        $this->addFlash('win', 'Payé');

        return $this->redirectToRoute('app_cart');

    }

    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clearCart(#[CurrentUser] Account $account, EntityManagerInterface $entityManager){

        $cart = $entityManager->getRepository(Cart::class)->findOneBy(["account"=>$account->getId(), "isPaid"=>false]);

        if($cart == null){
            $this->addFlash("error", "Impossible, pas de cart");
            return $this->redirectToRoute('app_cart');
        }

        foreach($cart->getItems() as $cartItem){
            $cartItem->getProduit()->setNumber($cartItem->getProduit()->getNumber()+$cartItem->getAmount());
            $entityManager->persist($cartItem->getProduit());
        }

        $entityManager->remove($cart);
        $entityManager->flush();
        $this->addFlash('win', 'Nettoyé');

        return $this->redirectToRoute('app_cart');

    }

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

        if($cart->getItems()->count() != 0) {

            $prixTotal = 0;
            foreach($cart->getItems() as $cartItem){
                $prixTotal = $prixTotal+($cartItem->getAmount()*$cartItem->getProduit()->getPrice());
            }

            return $this->render('cart/index.html.twig', [
                'controller_name' => 'CartController',
                'cartProducts' => $cart->getItems(),
                'message' => $message,
                'prixTotal' => $prixTotal
            ]);

        }else{
            return $this->render('cart/index.html.twig', [
                'controller_name' => 'CartController',
                'cartProducts' => $cart->getItems(),
                'message' => $message,
            ]);
        }
    }

    #[Route("/testRoute", name: "test")]
    public function getCartSize(#[CurrentUser] ?Account $account, EntityManagerInterface $entityManager) :Response
    {
        if($account == null)
            return $this->render("cart/cartsize.html.twig", ["cartSize"=>0]);
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(["account"=>$account->getId(), "isPaid"=>false]);
        if($cart == null)
            return $this->render("cart/cartsize.html.twig", ["cartSize"=>0]);

        $size = 0;
        foreach ($cart->getItems() as $item){
            $size+=$item->getAmount();
        }

        return $this->render("cart/cartsize.html.twig", ["cartSize"=>$size]);
    }
}
