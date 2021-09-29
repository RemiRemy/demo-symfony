<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'accueil')]
    public function index(): Response
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'Bienvenue',
        ]);
    }

    #[Route('/a-propos', name: 'a-propos')]
    public function aPropos(): Response
    {
        return $this->render('accueil/apropos.html.twig', [
            'test' => 'hello world',
        ]);
    }
}
