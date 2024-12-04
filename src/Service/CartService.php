<?php
// src/Service/CartService.php
namespace App\Service;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addToCart(Cart $cart, Product $product): void
    {
        $cart->getProducts()->add($product);
        $this->calculateTotal($cart);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    public function calculateTotal(Cart $cart): void
    {
        $total = 0;
        foreach ($cart->getProducts() as $product) {
            $total += $product->getPrice();
        }
        $cart->setTotal($total);
    }
}
