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
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Trouver le panier actuel (si disponible)
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['utilisateur' => $this->getUser()]);

        // Si aucun panier, en créer un
        if (!$cart) {
            $cart = new Cart();
            $cart->setTotal(0);
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        // Trouver tous les produits disponibles
        $produitsDisponibles = $this->produitRepository->findAll();

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'produitsDisponibles' => $produitsDisponibles,  // Passer les produits disponibles
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addProduct(int $id, EntityManagerInterface $entityManager): Response
    {
        // Trouver le produit à ajouter au panier
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['utilisateur' => $this->getUser()]);

        if ($cart && $produit) {
            $cart->addProduit($produit);
            $cart->setTotal($cart->calculateTotal());
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cart_index');
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
}