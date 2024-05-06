<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Cart;
use App\Entity\Produit;
use App\Entity\ProduitCart;
use App\Form\AddItemFormType;
use App\Form\ProductCartAddFormType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/produits', name: 'app_produits')]
class ProduitsController extends AbstractController
{

    public function addItem(Account $account, FormInterface $form, EntityManagerInterface $produitRepository): void{

        $cartRepository = $produitRepository->getRepository(Cart::class);
        $cart = $cartRepository->findOneBy(["account"=>$account->getId(), "isPaid"=>false]);

        if($cart == null){
            $cart = new Cart();
            $cart->setPaid(false);
            $cart->setAccount($account);
        }

        $produitRepository->persist($cart);

        $product = $produitRepository->getRepository(Produit::class)->findOneBy(["id"=>$form->get('item_id')->getData()]);
        if($product == null){
            $produitRepository->flush();
            $this->addFlash('error', 'Le produit est introuvable');
            return;
        }

        $amount = $form->get('item_number')->getData();

        if($product->getNumber() < $amount){
            $produitRepository->flush();
            $this->addFlash('error', 'Impossible, pas assez de produit');
            return;
        }

        $product->setNumber($product->getNumber()-$amount);

        $productCart = new ProduitCart();
        $productCart->setAmount($amount);
        $productCart->setProduit($product);
        $productCart->setCart($cart);
        $productCart->setPays($product->getPays()->first());

        $cart->addItem($productCart);

        $produitRepository->persist($cart);
        $produitRepository->persist($product);
        $produitRepository->persist($productCart);

        $produitRepository->flush();

        $this->addFlash('win', 'Dans le panier');

    }

    #[Route('/', name: '_p')]
    public function index(#[CurrentUser] ?Account $account, Request $request, EntityManagerInterface $em, ?string $message): Response
    {

        $products = $em->getRepository(Produit::class)->findAll();

        if(!is_null($account)){

            $tab = [];
            $cnt = 0;

            $curForm = $this->createForm(ProductCartAddFormType::class);
            $curForm->handleRequest($request);
            $printMessage = "DEF";

            if($message)
                $printMessage = $message;

            if($curForm->isSubmitted() && $curForm->isValid()){
                $this->addItem($account, $curForm, $em);
                return $this->redirectToRoute('app_produits_p');
            }

            foreach ($products as $product){
                $createdForm = $this->createForm(ProductCartAddFormType::class);
                $createdForm->get('item_id')->setData($product->getId());
                $tab[$cnt] = $createdForm->createView();
                $cnt++;
            }

            if($printMessage == null) {
                return $this->render('produits/index.html.twig', [
                    'controller_name' => 'ProduitsController',
                    'products' => $products,
                    'forms' => $tab
                ]);
            }else{
                return $this->render('produits/index.html.twig', [
                    'controller_name' => 'ProduitsController',
                    'products' => $products,
                    'forms' => $tab,
                    'message' => $printMessage
                ]);
            }

        }

        return $this->render('produits/index.html.twig', [
            'controller_name' => 'ProduitsController',
            'products' => $products
        ]);
    }
}
