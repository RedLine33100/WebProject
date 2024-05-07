<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Pays;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{

    public function update(#[CurrentUser] Account $account, UserPasswordHasherInterface $passwordHasher, FormInterface $form, EntityManagerInterface $entityManager)
    {

        if($form->get('username')->getData() != null){
            $account->setUsername($form->get('username')->getData());
        }

        if($form->get('address')->getData() != null){
            $account->setAddress($form->get('address')->getData());
        }

        if($form->get('email')->getData() != null){
            $account->setEmail($form->get('email')->getData());
        }

        if($form->get('lastname')->getData() != null){
            $account->setLastname($form->get('lastname')->getData());
        }

        if($form->get('password')->getData() != null){
            $account->setPassword($passwordHasher->hashPassword($account, $form->get('password')->getData()));
        }

        if($form->get('birthdate')->getData() != null){
            $account->setBirthDate($form->get('birthdate')->getData());
        }

        if($form->get('pays')->getData() != null){
            $pays = $entityManager->getRepository(Pays::class)->findOneBy(["id"=>$form->get('pays')->getData()]);
            if($pays != null)
                $account->setPays($pays);
            else{
                $this->addFlash('error', 'Le pays n\'existe pas');
                return;
            }
        }

        $entityManager->persist($account);
        $entityManager->flush();

        $this->addFlash('win', 'Updated !!');

    }

    #[Route('/user', name: 'app_user')]
    public function index(#[CurrentUser] Account $account, UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->update($account, $passwordHasher, $form, $entityManager);
            if($this->isGranted("ROLE_ADMIN")) {
                return $this->redirectToRoute("app_welcome");
            }else{
                return $this->redirectToRoute("app_produits_p");
            }
        }

        if($form->get('username')->getData() == null){
            $form->get('username')->setData($account->getUsername());
        }

        if($form->get('address')->getData() == null){
            $form->get('address')->setData($account->getAddress());
        }

        if($form->get('email')->getData() == null){
            $form->get('email')->setData($account->getEmail());
        }

        if($form->get('pays')->getData() == null){
            $form->get('pays')->setData($account->getPays()->getId());
        }

        if($form->get('lastname')->getData() == null){
            $form->get('lastname')->setData($account->getLastname());
        }

        if($form->get('birthdate')->getData() == null){
            $form->get('birthdate')->setData($account->getBirthDate());
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'forms'=>$form->createView()
        ]);
    }
}
