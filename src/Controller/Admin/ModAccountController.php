<?php

namespace App\Controller\Admin;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ModAccountController extends AbstractController
{

    public function generateModoForm(?Account $account):FormInterface
    {
        $form = $this->createFormBuilder();
        if($account == null) {
            $form->add('account_id', HiddenType::class);
        }else{
            $form->add('account_id', HiddenType::class, ['attr'=>['value'=>$account->getId()]]);
        }
        $form->add('send', SubmitType::class, ['label'=>'Supprimer']);
        return $form->getForm();
    }

    public function deleteUser(FormInterface $form, EntityManagerInterface $entityManager)
    {
        $accountID = $form->get('account_id')->getData();
        $account = $entityManager->getRepository(Account::class)->findOneBy(["id"=>$accountID]);
        if($account == null){
            return;
        }
        $entityManager->remove($account);
        $entityManager->flush();
    }

    #[Route('/modo/account', name: 'app_modo_account')]
    public function index(#[CurrentUser] Account $account, Request $request, EntityManagerInterface $entityManager, AccountRepository $accountRepository): Response
    {

        if(!in_array('ROLE_MOD', $account->getRoles(), true)){
            return $this->redirectToRoute('app_produits_p');
        }

        $testForm = $this->generateModoForm(null);
        $testForm->handleRequest($request);
        if($testForm->isSubmitted() && $testForm->isValid()){
            $this->deleteUser($testForm, $entityManager);
            return $this->redirectToRoute('app_modo_account');
        }

        $accounts = $accountRepository->findByRole("ROLE_USER");
        $forms = [];

        $cnt = 0;

        foreach ($accounts as $checkAccount){
            if(!in_array('ROLE_MOD', $checkAccount->getRoles(), true)) {
                $forms[$cnt] = $this->generateModoForm($checkAccount)->createView();
            }
            $cnt++;
        }

        return $this->render('mod/moduser.html.twig', [
            'controller_name' => 'ModAccountController',
            'accounts' => $accounts,
            'forms'=>$forms
        ]);
    }

    public function generateAdminForm(?Account $account) : FormInterface{
        $form = $this->createFormBuilder();
        if($account == null) {
            $form->add('account_id', HiddenType::class);
        }else{
            $form->add('account_id', HiddenType::class, ['attr'=>['value'=>$account->getId()]]);
        }
        if(in_array('ROLE_MOD', $account->getRoles(), true)) {
            $form->add('send', SubmitType::class, ['label' => 'Demote']);
        }else{
            $form->add('send', SubmitType::class, ['label' => 'Promote']);
        }
        return $form->getForm();
    }

    public function updateAdmin(FormInterface $form, EntityManagerInterface $entityManager) : void
    {
        $accountID = $form->get('account_id')->getData();
        $account = $entityManager->getRepository(Account::class)->findOneBy(["id"=>$accountID]);
        if($account == null){
            return;
        }
        if(in_array('ROLE_MOD', $account->getRoles(), true))
            $account->removeRole("ROLE_MOD");
        else
            $account->addRole("ROLE_MOD");
        $entityManager->persist($account);
        $entityManager->flush();
    }

    #[Route('/admin/account', name: 'app_admin_account')]
    public function adminManager(#[CurrentUser] Account $account, Request $request, EntityManagerInterface $entityManager, AccountRepository $accountRepository): Response
    {

        if(!in_array('ROLE_ADMIN', $account->getRoles(), true)){
            return $this->redirectToRoute('app_produits_p');
        }

        $testForm = $this->generateAdminForm(null);
        $testForm->handleRequest($request);
        if($testForm->isSubmitted() && $testForm->isValid()){
            $this->updateAdmin($testForm, $entityManager);
            return $this->redirectToRoute('app_admin_account');
        }

        $accounts = $accountRepository->findByRole("ROLE_USER");
        $forms = [];

        $cnt = 0;

        foreach ($accounts as $checkAccount){
            if(!in_array('ROLE_ADMIN', $checkAccount->getRoles(), true)) {
                $forms[$cnt] = $this->generateAdminForm($checkAccount)->createView();
            }
            $cnt++;
        }

        return $this->render('mod/moduser.html.twig', [
            'controller_name' => 'ModAccountController',
            'accounts' => $accounts,
            'forms'=>$forms
        ]);
    }

}
