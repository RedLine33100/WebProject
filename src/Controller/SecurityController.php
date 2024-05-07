<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Pays;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        $this->addFlash("win", "Déconnecté");
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Account();
        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $pays = $entityManager->getRepository(Pays::class)->findOneBy(["id"=>$form->get('pays')->getData()]);

            if($pays != null) {

                $user->setPays($pays);
                $user->setUsername($form->get('username')->getData());
                $user->setAddress($form->get('address')->getData());
                $user->setEmail($form->get('email')->getData());
                $user->setLastname($form->get('lastname')->getData());
                $user->setBirthDate($form->get('birthdate')->getData());

                if($form->get('password')->getData()->length()<3 or $form->get('password')->getData()->length()>30){
                    $this->addFlash('error', 'Password doit être entre 3 et 30 char');
                    return $this->redirectToRoute('app_register');
                }

                if($user->getUsername() == $form->get('password')->getData()){
                    $this->addFlash('error', 'Username et password doivent être différent');
                    return $this->redirectToRoute('app_register');
                }

                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                $constraintViolation = $validator->validate($user);
                if($constraintViolation->count() != 0){

                    $message = "";
                    $cntError = 1;

                    foreach ($constraintViolation as $violation){
                        $message = $message . $cntError . ": " . $violation->getMessage() . "<br>";
                        $cntError++;
                    }

                    $this->addFlash("error", $message);
                    return $this->redirectToRoute('app_register');

                }

                $entityManager->persist($user);
                $entityManager->flush();

                // do anything else you need here, like send an email

                $this->addFlash("win", "Compte crée");

                return $security->login($user, 'form_login', 'main');

            }
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
