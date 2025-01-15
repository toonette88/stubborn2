<?php
namespace App\Tests\Entity;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\CartItem;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartTest extends KernelTestCase
{
    public function testCartCanAddItems(): void
    {
        // Initialiser les entités
        $cart = new Cart();
        $product = (new Product())
            ->setName('T-shirt')
            ->setPrice(19.99)
            ->setDescription('A cool T-shirt')
            ->setStockM(10);
        
        $item = (new CartItem())
            ->setProduct($product)
            ->setQuantity(2)
            ->setSize('M');

        // Ajouter un élément au panier
        $cart->addItem($item);

        // Assertions
        $this->assertCount(1, $cart->getItems());
        $this->assertSame($item, $cart->getItems()->first());
        $this->assertEquals(2, $item->getQuantity());
        $this->assertEquals('M', $item->getSize());
    }

    public function testCartCanSetUser(): void
    {
        $cart = new Cart();
        $user = (new User())->setName('John Doe')->setEmail('john.doe@example.com');

        $cart->setUser($user);

        $this->assertSame($user, $cart->getUser());
    }
}
