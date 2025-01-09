<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\CartItem;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    private CartRepository $cartRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(CartRepository $cartRepository, EntityManagerInterface $entityManager)
    {
        $this->cartRepository = $cartRepository;
        $this->entityManager = $entityManager;
    }

    public function getOrCreateCart(User $user): Cart
    {
        $cart = $this->cartRepository->findOneBy(['user' => $user]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }

        return $cart;
    }
    public function calculateTotal(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getProduct()->getPrice() * $item->getQuantity();
        }
        return $total;
    }

    public function addProductToCart(User $user, Product $product, string $size, int $quantity): void
    {
        $cart = $this->getOrCreateCart($user);

        // Validation de la taille et du stock
        $stockField = 'stock' . strtoupper($size);

        if (!method_exists($product, 'get' . ucfirst($stockField))) {
            throw new \LogicException('Taille non valide.');
        }

        $availableStock = $product->{'get' . ucfirst($stockField)}();
        if ($quantity > $availableStock) {
            throw new \LogicException('Stock insuffisant pour la taille sélectionnée.');
        }

        // Mise à jour du stock produit
        $product->{'set' . ucfirst($stockField)}($availableStock - $quantity);

        // Ajout ou mise à jour du produit dans le panier
        $cartItem = $this->cartRepository->findCartItem($cart, $product, $size);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart)
                    ->setProduct($product)
                    ->setQuantity($quantity)
                    ->setSize($size);
            $this->entityManager->persist($cartItem);
        }

        $this->entityManager->flush();
    }

    public function removeProductFromCart(User $user, int $cartItemId): void
    {
        $cartItem = $this->entityManager->getRepository(CartItem::class)->find($cartItemId);

        if (!$cartItem || $cartItem->getCart()->getUser() !== $user) {
            throw new \LogicException('Produit non trouvé dans votre panier.');
        }

        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();
    }

}
