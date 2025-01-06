<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public const PRODUCT_REFERENCE = 'product-';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName('Product ' . $i)
                ->setDescription('Description for product ' . $i)
                ->setPrice(mt_rand(10, 100))
                ->setStockXS(mt_rand(0, 10))
                ->setStockS(mt_rand(0, 10))
                ->setStockM(mt_rand(0, 10))
                ->setStockL(mt_rand(0, 10))
                ->setStockXL(mt_rand(0, 10));

            $manager->persist($product);

            // Ajoutez une référence unique pour chaque produit
            $this->addReference(self::PRODUCT_REFERENCE . $i, $product);
        }

        $manager->flush();
    }
}
