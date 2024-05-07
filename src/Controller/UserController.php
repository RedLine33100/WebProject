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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{

    public function update(#[CurrentUser] Account $account, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher, FormInterface $form, EntityManagerInterface $entityManager): bool
    {

        if(strlen($form->get('password')->getData())<3 or strlen($form->get('password')->getData())>30){
            $this->addFlash('error', 'Password doit être entre 3 et 30 char');
            return false;
        }

        if($form->get('username')->getData() == $form->get('password')->getData()){
            $this->addFlash('error', 'Username et password doivent être différent');
            return false;
        }

        if($form->get('pays')->getData() != null){
            $pays = $entityManager->getRepository(Pays::class)->findOneBy(["id"=>$form->get('pays')->getData()]);
            if($pays != null)
                $account->setPays($pays);
            else{
                $this->addFlash('error', 'Le pays n\'existe pas');
                return false;
            }
        }

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

        $constraintViolation = $validator->validate($account);
        if($constraintViolation->count() != 0){

            $message = "";
            $cntError = 1;

            foreach ($constraintViolation as $violation){
                $message = $message . $cntError . ": " . $violation->getMessage() . "<br>";
                $cntError++;
            }

            $this->addFlash("error", $message);
            return false;

        }

        $entityManager->persist($account);
        $entityManager->flush();

        $this->addFlash('win', 'Updated !!');

        return true;

    }

    #[Route('/user', name: 'app_user')]
    public function index(#[CurrentUser] Account $account, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($this->update($account, $validator, $passwordHasher, $form, $entityManager)) {
                if ($account->getAccountType() == 2) {
                    return $this->redirectToRoute("app_welcome");
                } else {
                    return $this->redirectToRoute("app_produits_p");
                }
            }else{
                return $this->redirectToRoute('app_user');
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
