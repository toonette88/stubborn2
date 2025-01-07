<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\UserFactory;
use App\Factory\ProductFactory;
use App\Factory\CartFactory;
use App\Factory\CartItemFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(10);
        ProductFactory::createMany(15);
        CartFactory::createMany(5);
        CartItemFactory::createMany(10);
    }
}