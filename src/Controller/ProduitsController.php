<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Cart;
use App\Entity\Produit;
use App\Entity\ProduitCart;
use App\Form\AddItemFormType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function generateForm(?Produit $produit):FormInterface
    {
        $form = $this->createFormBuilder();
        if($produit == null) {
            $form->add('item_id', IntegerType::class, ['disabled' => true]);
        }else{
            $form->add('item_id', IntegerType::class, ['disabled' => true, 'attr'=>['value'=>$produit->getId()]]);
        }
        $form->add('item_number', IntegerType::class);
        $form->add('send', SubmitType::class, ['label'=>'Ajouter']);
        return $form->getForm();
    }

    public function addItem(Account $account, FormInterface $form, EntityManagerInterface $produitRepository): Response{

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
            return $this->redirectToRoute('app_welcome');
        }

        $productCart = new ProduitCart();
        $productCart->setAmount($form->get('item_number')->getData());
        $productCart->setProduit($product);
        $productCart->setCart($cart);
        $productCart->setPays($product->getPays()->first());

        $cart->addItem($productCart);

        $produitRepository->persist($cart);
        $produitRepository->persist($productCart);

        $produitRepository->flush();

        return $this->redirectToRoute('app_cart');

    }

    #[Route('/', name: '_p')]
    public function index(#[CurrentUser] ?Account $account, Request $request, EntityManagerInterface $em): Response
    {

        $products = $em->getRepository(Produit::class)->findAll();

        if(!is_null($account)){

            $tab = [];
            $cnt = 0;

            $curForm = $this->generateForm(null);
            $curForm->handleRequest($request);
            if($curForm->isSubmitted() && $curForm->isValid()){
                return $this->addItem($account, $curForm, $em);
            }

            foreach ($products as $product){
                $tab[$cnt] = $this->generateForm($product)->createView();
                $cnt++;
            }

            $message = null;

            if($message == null) {
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
                    'message' => $message
                ]);
            }

        }

        return $this->render('produits/index.html.twig', [
            'controller_name' => 'ProduitsController',
            'products' => $products
        ]);
    }
}
