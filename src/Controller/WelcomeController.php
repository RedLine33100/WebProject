<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class WelcomeController extends AbstractController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    #[Route('/', name: 'app_welcome')]
    public function index(): Response
    {
        // Les données au fichier Twig
        $donnees = [];

        if(isset($_GET['username'])) {
            $donnees['user'] = $_GET["username"];
        } else {
            $donnees['user'] = "Anonymous";
        }

        // Rendre le fichier Twig avec les données
        $contenu = $this->twig->render('welcome.html.twig', $donnees);

        // Retourner une réponse HTTP avec le contenu rendu
        return new Response($contenu);
    }
}