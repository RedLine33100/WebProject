<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Cart;
use App\Entity\Produit;
use App\Entity\ProduitCart;
use App\Form\ProductCartAddFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/produits', name: 'app_produits')]
class ProduitsController extends AbstractController
{

    public function addItem(Account $account, ValidatorInterface $validator, FormInterface $form, EntityManagerInterface $entityManager): void{

        $cartRepository = $entityManager->getRepository(Cart::class);
        $cart = $cartRepository->findOneBy(["account"=>$account->getId(), "isPaid"=>false]);

        if($cart == null){
            $cart = new Cart();
            $cart->setPaid(false);
            $cart->setAccount($account);
        }

        $entityManager->persist($cart);

        $product = $entityManager->getRepository(Produit::class)->findOneBy(["id"=>$form->get('item_id')->getData()]);
        if($product == null){
            $entityManager->flush();
            $this->addFlash('error', 'Le produit est introuvable');
            return;
        }

        $amount = $form->get('item_number')->getData();

        if($amount>0) {

            if ($product->getNumber() < $amount) {
                $entityManager->flush();
                $this->addFlash('error', 'Impossible, pas assez de produit');
                return;
            }

            $product->setNumber($product->getNumber() - $amount);

            $productCart = new ProduitCart();
            $productCart->setAmount($amount);
            $productCart->setProduit($product);
            $productCart->setCart($cart);
            $productCart->setPays($product->getPays()->first());

            $constraintViolation = $validator->validate($productCart);
            if($constraintViolation->count() != 0){

                $message = "";
                $cntError = 1;

                foreach ($constraintViolation as $violation){
                    $message = $message . $cntError . ": " . $violation->getMessage() . "<br>";
                    $cntError++;
                }

                $this->addFlash("error", $message);
                return ;

            }

            $cart->addItem($productCart);

            $entityManager->persist($cart);
            $entityManager->persist($product);
            $entityManager->persist($productCart);

            $entityManager->flush();

            $this->addFlash('win', 'Dans le panier');

        }else{

            $productCart = $entityManager->getRepository(ProduitCart::class)->findOneBy(["produit"=>$product->getId(), "cart"=>$cart->getId()]);

            if($productCart == null){
                $this->addFlash('error', 'Impossible, vous n\'en avez pas dans le panier');
                return;
            }

            $amount = $amount*-1;

            if($productCart->getAmount()<$amount){
                $this->addFlash('error', 'Impossible, pas assez de produit dans le panier');
                return;
            }

            if($productCart->getAmount() == $amount){

                $productCart->getProduit()->setNumber($productCart->getProduit()->getNumber()+$productCart->getAmount());

                $constraintViolation = $validator->validate($productCart);
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

                $cart->removeItem($productCart);
                $entityManager->remove($productCart);
                $entityManager->persist($cart);
                $entityManager->persist($productCart->getProduit());
                $entityManager->flush();


                $this->addFlash('win', 'Produit retiré');
                return;

            }

            $product->setNumber($product->getNumber() + $amount);
            $productCart->setAmount($productCart->getAmount()-$amount);

            $entityManager->persist($product);
            $entityManager->persist($productCart);

            $entityManager->flush();

            $this->addFlash('win', 'Produit retiré');

        }

    }

    #[Route('/', name: '_p')]
    public function index(#[CurrentUser] ?Account $account, ValidatorInterface $validator, Request $request, EntityManagerInterface $em, ?string $message): Response
    {

        $products = $em->getRepository(Produit::class)->findAll();

        if(!is_null($account)){

            if($account->getAccountType() == 2){
                $this->addFlash("error", "Pas autorisé");
                return $this->redirectToRoute('app_welcome');
            }

            $tab = [];
            $cnt = 0;

            $curForm = $this->createForm(ProductCartAddFormType::class);
            $curForm->handleRequest($request);

            if($curForm->isSubmitted() and $curForm->isValid()){
                $this->addItem($account, $validator, $curForm, $em);
                return $this->redirectToRoute('app_produits_p');
            }

            $cart = $em->getRepository(Cart::class)->findOneBy(["account"=>$account->getId(), "isPaid"=>false]);

            foreach ($products as $product){

                $productCart = null;
                if($cart != null)
                    $productCart = $em->getRepository(ProduitCart::class)->findOneBy(["produit"=>$product->getId(), "cart"=>$cart->getId()]);

                if($product->getNumber() <= 0 and $productCart == null){
                    $cnt++;
                    continue;
                }
                $createdForm = $this->createForm(ProductCartAddFormType::class);
                $createdForm->get('item_id')->setData($product->getId());
                $createdForm->get('item_number')->setData(0);
                $tab[$cnt] = $createdForm->createView();
                $cnt++;
            }

            return $this->render('produits/index.html.twig', [
                'controller_name' => 'ProduitsController',
                'products' => $products,
                'forms' => $tab
            ]);

        }

        return $this->render('produits/index.html.twig', [
            'controller_name' => 'ProduitsController',
            'products' => $products
        ]);
    }
}
