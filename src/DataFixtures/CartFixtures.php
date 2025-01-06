<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\DataFixtures\UserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CartFixtures extends Fixture implements DependentFixtureInterface
{
    public const CART_REFERENCE = 'cart-';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $cart = new Cart();

            // Récupère la référence de l'utilisateur correspondant
            $userReference = UserFixtures::USER_REFERENCE . $i;
            $cart->setUser($this->geteference($userReference, User::class));

            $manager->persist($cart);

            // Définit une référence unique pour chaque panier
            $this->addReference(self::CART_REFERENCE . $i, $cart);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
