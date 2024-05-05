<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        $fb = $this->createFormBuilder();
        $fb->add('item_number', IntegerType::class);
        $fb->add('item_id', IntegerType::class);
        $fb->add('send', SubmitType::class);
        return $fb->getForm();
    }

    public function addItem(Account $account, FormInterface $form, ProduitRepository $produitRepository): ?string{

        if(!$form->get('submit')->isClicked())
            return "NONE";

        return "re".$form->get('item_number')->getData();

    }

    #[Route('/', name: '_p')]
    public function index(#[CurrentUser] ?Account $account, Request $request, ProduitRepository $produitRepository): Response
    {

        $products = $produitRepository->findAll();

        if(!is_null($account)){

            $message = "Not Submitted";
            $cnt = 0;
            $forms = [];

            foreach ($products as $product){

                $form = $this->generateForm($product);
                $form->handleRequest($request);
                if($form->get('send')->isClicked())
                    $message = "SUBM";
                $forms[$cnt] = $form->createView();

                $cnt++;

            }

            if($message == null) {
                return $this->render('produits/index.html.twig', [
                    'controller_name' => 'ProduitsController',
                    'products' => $products,
                    'forms' => $forms
                ]);
            }else{
                return $this->render('produits/index.html.twig', [
                    'controller_name' => 'ProduitsController',
                    'products' => $products,
                    'forms' => $forms,
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
