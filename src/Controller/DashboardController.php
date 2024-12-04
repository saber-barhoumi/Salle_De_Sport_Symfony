<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ProduitRepository $produitRepository): Response
    {
        // Récupérer les données pour les statistiques
        $totalProduits = $produitRepository->count([]);
        $prixMoyen = $produitRepository->getAveragePrice();
        $produitMaxPrix = $produitRepository->getMaxPriceProduit();
        $produitMinPrix = $produitRepository->getMinPriceProduit();

        // Préparer les données pour les graphiques (par exemple : produits et leurs prix)
        $produits = $produitRepository->findAll();
        $chartData = [];
        foreach ($produits as $produit) {
            $chartData[] = [
                'nom' => $produit->getNom(),
                'prix' => $produit->getPrix(),
            ];
        }

        // Passer toutes les données nécessaires au template
        return $this->render('dashboard/index.html.twig', [
            'total_produits' => $totalProduits,
            'prix_moyen' => $prixMoyen,
            'produit_max_prix' => $produitMaxPrix ? $produitMaxPrix['nom'] . ' - ' . $produitMaxPrix['prix'] : null,
            'produit_min_prix' => $produitMinPrix ? $produitMinPrix['nom'] . ' - ' . $produitMinPrix['prix'] : null,
            'chart_data' => json_encode($chartData), // Encoder les données pour JavaScript
        ]);
    }
}

