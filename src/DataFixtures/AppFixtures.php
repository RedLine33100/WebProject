<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\Cart;
use App\Entity\Pays;
use App\Entity\Produit;
use App\Entity\ProduitCart;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {

        $account1 = new Account();
        $account1->setName("HugoDecrypte")
            ->setAddress("rue Hugo Decrypte, 33100 Paris")
            ->setEmail("contact@hugodecrypte.com")
            ->setPassword("\$2y\$13\$jkovLPSyQrl06kbkXLT7R.BzZYJZcPRkoNrw9ZcHR91P6CEFLHGpO");

        $manager->persist($account1);



        $account2 = new Account();
        $account2->setName("Modric")
            ->setEmail("jesuismodric@gmail.com")
            ->setAddress("Rue du quiditch, 15000 Poudlard")
            ->setPassword("\$2y\$13\$aOP40b8mH4znW7nMWIBILukPRxXuMkojO9F3fho4zCfwn948tRPdi");

        $manager->persist($account2);



        $account3 = new Account();
        $account3->setName("Lucas")
            ->setEmail("jemassumecommejesuis@defendonsnosdroits.org")
            ->setAddress("1 rue de la liberte, 80000 Paris")
            ->setPassword("\$2y\$13\$/Le5Xdhxi31Y22o48KoF2.WYJBnwvypTpOSlnMn7YuznZMWVHxkKC");

        $manager->persist($account3);


        $account4 = new Account();
        $account4->setName("Freedom")
            ->setEmail("pleasedonothelpmeimfree@gmail.com")
            ->setAddress("15 rue de la liberte, 14000 Ciel")
            ->setPassword("\$2y\$13\$QtpGMKOTaJlUYLB7u1SHa.9EzFGhjxP1BidTZhLmZOQUsR6XDQhly");

        $manager->persist($account4);



        $pays1 = new Pays();
        $pays1->setName("France")
            ->setShortName("FR");
        $manager->persist($pays1);

        $pays2 = new Pays();
        $pays2->setName("United States of America")
            ->setShortName("USA");
        $manager->persist($pays2);

        $pays3 = new Pays();
        $pays3->setName("Spain")
            ->setShortName("SP");
        $manager->persist($pays3);


        $produit1 = new Produit();
        $produit1->setName("Ananas")
            ->setDescription("Hmm les ananas")
            ->setPrice(5.0)
            ->addPay($pays3);

        $manager->persist($produit1);


        $produit2 = new Produit();
        $produit2->setName("Pates")
            ->setDescription("Les meilleurs Pates du monde !!!")
            ->setPrice(2.0)
            ->addPay($pays3)
            ->addPay($pays2)
            ->addPay($pays1);

        $manager->persist($produit2);


        $produit3 = new Produit();
        $produit3->setName("Orange")
            ->setDescription("Les meilleures oranges du monde (sauf USA)")
            ->setPrice(5.0)
            ->addPay($pays1)
            ->addPay($pays2);

        $manager->persist($produit3);


        $produit4 = new Produit();
        $produit4->setName("Paracetamol")
            ->setDescription("Ne pas en abuser, demander conseil au Pharmacien")
            ->setPrice(12.2)
            ->addPay($pays1);

        $manager->persist($produit4);


        $cart1 = new Cart();
        $cart1->setAccount($account1);

        $productCart1 = new ProduitCart();
        $productCart1->setCart($cart1)
            ->setPays($pays3)
            ->setProduit($produit1)
            ->setAmount(2);

        $manager->persist($productCart1);


        $productCart2 = new ProduitCart();
        $productCart2->setCart($cart1)
            ->setPays($pays2)
            ->setProduit($produit2)
            ->setAmount(5);

        $manager->persist($productCart2);


        $productCart3 = new ProduitCart();
        $productCart3->setCart($cart1)
            ->setPays($pays1)
            ->setProduit($produit3)
            ->setAmount(1);

        $manager->persist($productCart3);


        $productCart4 = new ProduitCart();
        $productCart4->setCart($cart1)
            ->setPays($pays1)
            ->setProduit($produit4)
            ->setAmount(2);

        $manager->persist($productCart4);



        $cart2 = new Cart();
        $cart2->setAccount($account1);

        $productCart1 = new ProduitCart();
        $productCart1->setCart($cart2)
            ->setPays($pays3)
            ->setProduit($produit1)
            ->setAmount(2);

        $manager->persist($productCart1);


        $productCart2 = new ProduitCart();
        $productCart2->setCart($cart2)
            ->setPays($pays1)
            ->setProduit($produit2)
            ->setAmount(10);

        $manager->persist($productCart2);



        $cart3 = new Cart();
        $cart3->setAccount($account2);

        $productCart1 = new ProduitCart();
        $productCart1->setCart($cart3)
            ->setPays($pays1)
            ->setProduit($produit3)
            ->setAmount(1);

        $manager->persist($productCart1);



        $cart4 = new Cart();
        $cart4->setAccount($account3);

        $productCart1 = new ProduitCart();
        $productCart1->setCart($cart4)
            ->setPays($pays1)
            ->setProduit($produit4)
            ->setAmount(50);

        $manager->persist($productCart1);


        $productCart2 = new ProduitCart();
        $productCart2->setCart($cart4)
            ->setPays($pays1)
            ->setProduit($produit2)
            ->setAmount(2);

        $manager->persist($productCart2);

        $productCart3 = new ProduitCart();
        $productCart3->setCart($cart4)
            ->setPays($pays2)
            ->setProduit($produit2)
            ->setAmount(50);

        $manager->persist($productCart3);


        $cart1->setPaid(true);
        $cart2->setPaid(true);
        $cart3->setPaid(true);
        $cart4->setPaid(false);


        $manager->persist($cart1);
        $manager->persist($cart2);
        $manager->persist($cart3);
        $manager->persist($cart4);

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
