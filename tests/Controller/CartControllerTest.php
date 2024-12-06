<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Cart;
use App\Entity\CartItem;
use PHPUnit\Framework\TestCase;

class CartControllerTest extends TestCase
{
    public function testAddProductToCart()
    {
        // Créer un mock pour l'entité Product
        $productMock = $this->createMock(Product::class);
        $productMock->method('getId')->willReturn(1);
        $productMock->method('getName')->willReturn('Test Product');

        // Créer un mock pour l'entité Cart
        $cartMock = $this->createMock(Cart::class);
        $cartMock->method('getId')->willReturn(1);

        // Créer un CartItem et assigner le produit et le panier
        $cartItem = new CartItem();
        $cartItem->setProduct($productMock);
        $cartItem->setCart($cartMock);
        $cartItem->setQuantity(2);
        $cartItem->setSize('M');

        // Test si le CartItem contient bien les valeurs
        $this->assertSame(1, $cartItem->getProduct()->getId());
        $this->assertSame('Test Product', $cartItem->getProduct()->getName());
        $this->assertSame(2, $cartItem->getQuantity());
        $this->assertSame('M', $cartItem->getSize());
    }
}
