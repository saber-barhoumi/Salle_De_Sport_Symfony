<?php
namespace App\Service;

use App\Repository\ProduitRepository;

class ProduitService
{
    private $produitRepository;

    // Injection du repository ProduitRepository dans le service
    public function __construct(ProduitRepository $produitRepository)
    {
        $this->produitRepository = $produitRepository;
    }

    // Méthode pour récupérer tous les produits
    public function getAllProduits()
    {
        return $this->produitRepository->findAll();
    }

    // Méthode pour filtrer les produits par critères
    public function filterProduitsByCriteria($nom, $categorie)
    {
        return $this->produitRepository->findByCriteria($nom, $categorie);  // Assurez-vous que la méthode findByCriteria existe dans le repository
    }
}
