<?php

namespace App\Controller\Admin;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ModAccountController extends AbstractController
{

    #[Route('/modo/account/delete/{id}', name: 'app_modo_account_delete')]
    public function deleteUser(#[CurrentUser] Account $currentAccount, EntityManagerInterface $entityManager, int $id):Response
    {
        if($currentAccount->getAccountType() != 1){
            return $this->redirectToRoute('app_produits_p');
        }

        if($currentAccount->getId() == $id){
            $this->addFlash("error", "Ne pas se supprimer soi-même");
            return $this->redirectToRoute('app_produits_p');
        }
        $account = $entityManager->getRepository(Account::class)->findOneBy(["id"=>$id]);
        if($account == null){
            $this->addFlash("error", "Aucun compte trouvé");
            return $this->redirectToRoute('app_produits_p');
        }

        if(in_array('ROLE_MOD', $account->getRoles(), true)){
            $this->addFlash("error", "Impossible");
        }

        $entityManager->remove($account);
        $entityManager->flush();
        $this->addFlash('win', 'User supprimé');
        return $this->redirectToRoute('app_modo_account');
    }

    #[Route('/modo/account', name: 'app_modo_account')]
    public function index(#[CurrentUser] Account $account, Request $request, EntityManagerInterface $entityManager, AccountRepository $accountRepository): Response
    {

        if($account->getAccountType() != 1){
            return $this->redirectToRoute('app_produits_p');
        }

        $accounts = $accountRepository->findByRole("ROLE_USER");
        $blValidate = [];

        $cnt = 0;

        foreach ($accounts as $checkAccount){
            $blValidate[$cnt] = !in_array('ROLE_MOD', $checkAccount->getRoles(), true);
            $cnt++;
        }

        return $this->render('mod/moduser.html.twig', [
            'controller_name' => 'ModAccountController',
            'accounts' => $accounts,
            'blValidate'=>$blValidate
        ]);
    }

    #[Route('/modo/account/switchmod/{id}', name: 'app_modo_account_switchmod')]
    public function switchMod(#[CurrentUser] Account $currentAccount, EntityManagerInterface $entityManager, int $id):Response
    {
        $account = $entityManager->getRepository(Account::class)->findOneBy(["id"=>$id]);
        if($account == null){
            $this->addFlash('error', 'No account');
            return $this->redirectToRoute('app_admin_account');
        }
        $mod = false;
        if($account->getAccountType() == 1)
            $account->setAccountType(0);
        else {
            $account->setAccountType(1);
            $mod = true;
        }
        $entityManager->persist($account);
        $entityManager->flush();
        if($mod)
            $this->addFlash('win', 'Le compte est devenue moderateur');
        else
            $this->addFlash('win', 'Le compte n\'est plus moderateur');


        return $this->redirectToRoute('app_admin_account');

    }

    #[Route('/admin/account', name: 'app_admin_account')]
    public function adminManager(#[CurrentUser] Account $account, Request $request, EntityManagerInterface $entityManager, AccountRepository $accountRepository): Response
    {

        if($account->getAccountType() != 2){
            return $this->redirectToRoute('app_produits_p');
        }

        $accounts = $accountRepository->findByRole("ROLE_USER");
        $boolMod = [];

        $cnt = 0;

        foreach ($accounts as $checkAccount){
            if($checkAccount->getAccountType() != 2) {

                if($checkAccount->getAccountType() == 1){
                    $boolMod[$cnt] = false;
                }else{
                    $boolMod[$cnt] = true;
                }

            }
            $cnt++;
        }

        return $this->render('mod/moduser.html.twig', [
            'controller_name' => 'ModAccountController',
            'accounts' => $accounts,
            'boolMod'=>$boolMod
        ]);
    }

}
