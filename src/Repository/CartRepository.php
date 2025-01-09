<?php

namespace App\Repository;

use App\Entity\Product;  // Assure-toi que tu utilises App\Entity\Product
use App\Entity\Cart;
use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function findCartItem(Cart $cart, Product $product, string $size): ?CartItem
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.items', 'i')
            ->where('i.product = :product')
            ->andWhere('i.size = :size')
            ->andWhere('c.id = :cart')
            ->setParameter('product', $product)
            ->setParameter('size', $size)
            ->setParameter('cart', $cart)
            ->getQuery()
            ->getOneOrNullResult();
    }
}