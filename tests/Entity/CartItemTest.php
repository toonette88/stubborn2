<?php

namespace App\Tests\Entity;

use App\Entity\CartItem;
use App\Entity\Cart;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class CartItemTest extends TestCase
{
    public function testCartItemProperties()
    {
        // Création de mocks ou d'objets réels pour Cart et Product
        $cart = new Cart(); 
        $product = new Product();

        // Créer une instance de CartItem
        $cartItem = new CartItem();

        // Initialiser la propriété size
        $cartItem->setSize('L');

        //Initialiser la quantité
        $cartItem->setQuantity('1');
        
        // Vérifier les valeurs initiales
        $this->assertNull($cartItem->getId());
        $this->assertNull($cartItem->getCart());
        $this->assertNull($cartItem->getProduct());
        $this->assertEquals(1,$cartItem->getQuantity());
        $this->assertEquals('L', $cartItem->getSize());  

        // Tester la méthode setCart()
        $cartItem->setCart($cart);
        $this->assertSame($cart, $cartItem->getCart());

        // Tester la méthode setProduct()
        $cartItem->setProduct($product);
        $this->assertSame($product, $cartItem->getProduct());

        // Tester la méthode setQuantity()
        $cartItem->setQuantity(2);
        $this->assertIsInt($cartItem->getQuantity()); 
        $this->assertEquals(2, $cartItem->getQuantity());

        // Tester la méthode setSize()
        $cartItem->setSize('M');
        $this->assertEquals('M', $cartItem->getSize());
    }

}
