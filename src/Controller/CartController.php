<?php
// src/Controller/CartController.php
namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\Order;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_show')]
    public function showCart(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        return $this->render('cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addToCart(Product $product, CartService $cartService, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $entityManager->persist($cart);
        }

        $cartService->addToCart($cart, $product);

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/checkout', name: 'cart_checkout')]
    public function checkout(Cart $cart, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $order = new Order();
        $order->setUser($user);
        $order->setProducts($cart->getProducts());
        $order->setTotal($cart->getTotal());
        $entityManager->persist($order);

        // Vider le panier
        $cart->getProducts()->clear();
        $cart->setTotal(0);
        $entityManager->flush();

        return $this->redirectToRoute('order_history');
    }

    #[Route('/orders', name: 'order_history')]
    public function orderHistory(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $orders = $entityManager->getRepository(Order::class)->findBy(['user' => $user]);

        return $this->render('order/history.html.twig', [
            'orders' => $orders,
        ]);
    }
}
