<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface; // Correct import
use Symfony\Component\HttpFoundation\RedirectResponse;

class CartController extends AbstractController
{    public function __construct(ProduitRepository $produitRepository)
    {
        $this->produitRepository = $produitRepository;
    }
    private ProduitRepository $produitRepository;

   


    // Route pour ajouter un produit au panier
    #[Route('/panier/ajouter/{id}', name: 'cart_add')]
    public function addToCart(int $id, Request $request): Response
    {
        $session = $request->getSession(); // Récupère la session
        $cart = $session->get('cart', []); // Récupère le panier depuis la session (tableau vide par défaut)
    
        // Vérifie si $cart est un tableau. Si ce n'est pas le cas, réinitialise le panier comme tableau vide
        if (!is_array($cart)) {
            $cart = [];
        }
    
        // Si le produit est déjà dans le panier, on augmente la quantité
        if (isset($cart[$id])) {
            $cart[$id]++; // Incrémente la quantité du produit
        } else {
            $cart[$id] = 1; // Ajoute le produit avec une quantité de 1
        }
    
        // Sauvegarde le panier dans la session
        $session->set('cart', $cart);
    
        // Redirige vers la vue du panier
        return $this->redirectToRoute('cart_view');
    }
    

    // Route pour afficher le contenu du panier
    
    #[Route('/panier', name: 'cart_view')]
    public function viewCart(Request $request): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []); // Récupère le panier depuis la session
    
        $produits = []; // Variable utilisée dans le template
        $total = 0; // Initialisation de la variable total
    
        // Calcul des produits dans le panier
        foreach ($cart as $productId => $quantity) {
            $produit = $this->produitRepository->find($productId); // Recherche le produit en base de données
            if ($produit) {
                // Ajoute le produit au panier
                $produits[] = [
                    'produit' => $produit, // Correspondance avec le template
                    'quantite' => $quantity, // Correspondance avec le template
                ];
    
                // Calcul du total
                $total += $produit->getPrix() * $quantity;
            }
        }
    
        // Rendu du template avec produits et total
        return $this->render('cart/view.html.twig', [
            'produits' => $produits, // Envoi des produits au template
            'total' => $total, // Envoi du total au template
        ]);
    }
    
    

// Route pour supprimer un produit du panier
#[Route('/panier/supprimer/{id}', name: 'cart_remove')]
public function removeFromCart(int $id, Request $request): RedirectResponse
{
    $session = $request->getSession();
    $cart = $session->get('cart', []); // Récupère le panier depuis la session

    // Vérifie que le panier est un tableau, sinon réinitialise-le
    if (!is_array($cart)) {
        $cart = [];
    }

    if (isset($cart[$id])) {
        unset($cart[$id]); // Retire le produit du panier
    }

    $session->set('cart', $cart); // Enregistre le panier modifié dans la session

    return $this->redirectToRoute('cart_view'); // Redirige vers la page du panier
}
}