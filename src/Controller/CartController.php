<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Cart;
use App\Entity\Produit;
use App\Entity\ProduitCart;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class CartController extends AbstractController
{
    public function generateForm(?ProduitCart $produitCart):FormInterface
    {
        $form = $this->createFormBuilder();
        if($produitCart == null) {
            $form->add('pID', HiddenType::class);
        }else{
            $form->add('pID', HiddenType::class, ['attr'=>["value"=>$produitCart->getId()]]);
        }
        $form->add('send', SubmitType::class, ['label'=>"Supprimer"]);
        return $form->getForm();
    }

    public function generatePaidForm():FormInterface
    {
        $form = $this->createFormBuilder();
        $form->add('paid', SubmitType::class, ['label'=>"Payer (Archiver)"]);
        return $form->getForm();
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

        $suppFormCheck = $this->generateForm(null);
        $suppFormCheck->handleRequest($request);

        if($suppFormCheck->isSubmitted() && $suppFormCheck->isValid()){

            $pCartID = $suppFormCheck->get('pID')->getData();
            $pCart = $entityManager->getRepository(ProduitCart::class)->findOneBy(["id"=>$pCartID]);

            if($pCart != null){
                $cart->removeItem($pCart);
                $entityManager->remove($pCart);
                $entityManager->persist($cart);
                $entityManager->flush();

                return $this->redirectToRoute('app_cart');

            }else {

                return $this->redirectToRoute('app_welcome');

            }

        }

        $suppForms = [];
        $cnt = 0;
        foreach ($cart->getItems() as $pCart){

            $suppForms[$cnt] = $this->generateForm($pCart)->createView();
            $cnt++;

        }

        $paidForm = $this->generatePaidForm();
        $paidForm->handleRequest($request);

        if($paidForm->isSubmitted()){
            $cart->setPaid(true);
            $entityManager->persist($cart);
            $entityManager->flush();

            return $this->redirectToRoute('app_cart');
        }

        if($cart->getItems()->count() != 0) {

            return $this->render('cart/index.html.twig', [
                'controller_name' => 'CartController',
                'cartProducts' => $cart->getItems(),
                'message' => $message,
                'suppForms' => $suppForms,
                'paidButton' => $paidForm->createView()
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
