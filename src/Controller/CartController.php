<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        $total = array_reduce(
            $cart->getItems()->toArray(),
            fn($sum, CartItem $item) => $sum + $item->getProduct()->getPrice() * $item->getQuantity(),
            0
        );

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]) ?? new Cart();
        $cart->setUser($user);

        $size = $request->request->get('size');
        $quantity = (int) $request->request->get('quantity', 1);

        // Validation de la taille et du stock
        $stockField = 'stock' . strtoupper($size);
        if (!method_exists($product, 'get' . ucfirst($stockField))) {
            $this->addFlash('error', 'Taille non valide.');
            return $this->redirectToRoute('app_product_detail', ['id' => $product->getId()]);
        }

        $availableStock = $product->{'get' . ucfirst($stockField)}();
        if ($quantity > $availableStock) {
            $this->addFlash('error', 'Stock insuffisant pour la taille sélectionnée.');
            return $this->redirectToRoute('app_product_detail', ['id' => $product->getId()]);
        }

        // Mise à jour du stock
        $product->{'set' . ucfirst($stockField)}($availableStock - $quantity);

        $cartItem = $entityManager->getRepository(CartItem::class)->findOneBy([
            'cart' => $cart,
            'product' => $product,
            'size' => $size,
        ]);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart)
                ->setProduct($product)
                ->setQuantity($quantity)
                ->setSize($size);
            $entityManager->persist($cartItem);
        }

        $entityManager->persist($cart);
        $entityManager->flush();

        $this->addFlash('success', 'Produit ajouté au panier.');
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cartItem = $entityManager->getRepository(CartItem::class)->find($id);

        if ($cartItem && $cartItem->getCart()->getUser() === $user) {
            $entityManager->remove($cartItem);
            $entityManager->flush();
            $this->addFlash('success', 'Produit retiré du panier.');
        } else {
            $this->addFlash('error', 'Produit non trouvé dans votre panier.');
        }

        return $this->redirectToRoute('app_cart');
    }
}
