<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/account', name: 'app_account')]
class AccountController extends AbstractController
{
    #[Route('/login', name: '_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('account/login.html.twig', [
              'controller_name' => 'LoginController',
              'last_username' => $lastUsername,
              'error'         => $error,
        ]);
    }

    #[Route('/logout', name: '_logout')]
    public function logout(Security $security): Response
    {
        $security->logout();
        return $this->redirectToRoute('app_account_login');
    }

    #[Route('/register', name: '_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Account();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setName($form->get('name')->getData());
            $user->setAddress($form->get('address')->getData());
            $user->setEmail($form->get('email')->getData());

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('account/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
