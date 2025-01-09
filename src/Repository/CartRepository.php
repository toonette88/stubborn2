<?php
namespace App\Repository;

use App\Entity\CartItem;
use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    /**
     * Trouve un CartItem par son panier, produit, et taille
     */
    public function findCartItem(Cart $cart, Product $product, string $size): ?CartItem
    {
        return $this->getEntityManager()
            ->getRepository(CartItem::class)
            ->findOneBy([
                'cart' => $cart,
                'product' => $product,
                'size' => $size,
            ]);
    }
}