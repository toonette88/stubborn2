<?php

namespace App\DataFixtures;

use App\Entity\CartItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CartItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $cartItem = new CartItem();
            $cartItem->setCart($this->getReference(CartFixtures::CART_REFERENCE . mt_rand(0, 4)));
            $cartItem->setProduct($this->getReference(ProductFixtures::PRODUCT_REFERENCE . mt_rand(0, 9)));
            $cartItem->setQuantity(mt_rand(1, 5));
            $cartItem->setSize(['XS', 'S', 'M', 'L', 'XL'][array_rand(['XS', 'S', 'M', 'L', 'XL'])]);

            $manager->persist($cartItem);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CartFixtures::class,
            ProductFixtures::class,
        ];
    }
}
