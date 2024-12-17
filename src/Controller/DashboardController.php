<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;

class DashboardController extends AbstractController
{#[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
