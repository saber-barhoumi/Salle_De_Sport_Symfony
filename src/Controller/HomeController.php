<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UtilisateurRepository;


class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('base2.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('home/utilisateur/{id}', name: 'app_utilisateur_show2')]
public function showUtilisateur2(UtilisateurRepository $rep, int $id): Response
{
    $utilisateur = $rep->find($id);

    if (!$utilisateur) {
        throw $this->createNotFoundException('Utilisateur non trouvé.');
    }

    return $this->render('home/showUser.html.twig', [
        'utilisateur' => $utilisateur,
    ]);
}
   
}
