<?php
namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Produit;
use App\Repository\CartRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface; // Ajoutez cette ligne en haut pour l'importation

class CartController extends AbstractController
{
    private $cartRepository;
    private $produitRepository;
    private $entityManager;

    public function __construct(CartRepository $cartRepository, ProduitRepository $produitRepository, EntityManagerInterface $entityManager)
    {
        $this->cartRepository = $cartRepository;
        $this->produitRepository = $produitRepository;
        $this->entityManager = $entityManager;
    }

    
    #[Route('/cart', name: 'cart_index')]
    public function index(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le panier depuis la session
        $cart = $session->get('cart');
    
        // Si le panier n'existe pas ou n'est pas un objet Cart, on crée un nouvel objet Cart
        if (!$cart || !$cart instanceof Cart) {
            $cart = new Cart();  // Créer un nouvel objet Cart
            $session->set('cart', $cart);  // Sauvegarder le panier dans la session
        }
    
        // Calculer le total du panier
        $total = $cart->calculateTotal();  // Utilisez la méthode calculateTotal() pour calculer le total
    
        // Trouver tous les produits disponibles
        $produitsDisponibles = $entityManager->getRepository(Produit::class)->findAll();
    
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,                // Passez le panier au template
            'total' => $total,              // Passez le total au template
            'produitsDisponibles' => $produitsDisponibles,  // Passez les produits disponibles
        ]);
    }
    
    // Lors de l'ajout au panier, on persiste le panier dans la base de données
#[Route('/cart/add/{id}', name: 'cart_add')]
public function addProduct(int $id, Request $request, EntityManagerInterface $entityManager): Response
{
    // Trouver le produit à ajouter au panier
    $produit = $entityManager->getRepository(Produit::class)->find($id);
    
    if (!$produit) {
        return $this->json([
            'success' => false,
            'message' => 'Produit non trouvé'
        ], 404);
    }

    // Vérifier si un panier existe déjà pour cet utilisateur
    $cart = $entityManager->getRepository(Cart::class)->findOneBy(['utilisateur' => $user]);

    if (!$cart) {
        // Créer un nouveau panier si aucun panier n'existe pour cet utilisateur
        $cart = new Cart();
        $cart->setUtilisateur($user);
    }

    // Ajouter le produit au panier
    $cart->addProduit($produit);

    // Mettre à jour le total du panier
    $cart->setTotal($cart->calculateTotal());

    // Sauvegarder les modifications dans la base de données
    $entityManager->persist($cart);
    $entityManager->flush();

    // Retourner une réponse JSON avec succès
    return $this->json([
        'success' => true,
        'message' => 'Produit ajouté au panier',
        'total' => $cart->getTotal()
    ]);
}

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function removeProduct(int $id, EntityManagerInterface $entityManager): Response
    {
        // Trouver le produit à retirer du panier
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['utilisateur' => $this->getUser()]);

        if ($cart && $produit) {
            $cart->removeProduit($produit);
            $cart->setTotal($cart->calculateTotal());
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cart_index');
    }
    #[Route('/cart/info', name: 'cart_info')]
public function getCartInfo(EntityManagerInterface $entityManager): Response
{
    // Récupérer le panier de l'utilisateur connecté
    $user = $this->getUser();
    $cart = $entityManager->getRepository(Cart::class)->findOneBy(['utilisateur' => $user]);

    $cartCount = 0;
    $cartTotal = 0;

    if ($cart) {
        $cartCount = count($cart->getProduits());
        $cartTotal = $cart->getTotal();
    }

    // Retourner les informations du panier en JSON
    return $this->json([
        'count' => $cartCount,
        'total' => $cartTotal
    ]);
}

}