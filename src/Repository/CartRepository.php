<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    /**
     * Trouver un panier par utilisateur
     */
    public function findByUtilisateur(Utilisateur $utilisateur): ?Cart
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouver un panier contenant un produit spécifique
     */
    public function findByProduit(Produit $produit): ?Cart
    {
        return $this->createQueryBuilder('c')
            ->join('c.produits', 'p')
            ->andWhere('p.id = :produit')
            ->setParameter('produit', $produit)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Ajouter un produit dans le panier
     */
    public function addProduitToCart(Cart $cart, Produit $produit): void
    {
        if (!$cart->getProduits()->contains($produit)) {
            $cart->addProduit($produit);
            $this->_em->flush();
        }
    }

    /**
     * Retirer un produit du panier
     */
    public function removeProduitFromCart(Cart $cart, Produit $produit): void
    {
        if ($cart->getProduits()->contains($produit)) {
            $cart->removeProduit($produit);
            $this->_em->flush();
        }
    }

    /**
     * Calculer le total du panier
     */
    public function calculateTotal(Cart $cart): float
    {
        return $cart->getTotal();
    }
}
