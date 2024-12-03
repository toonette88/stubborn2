<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart')]
    public function index(EntityManagerInterface $entityManager, UserInterface $user): Response
{
    $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

    if (!$cart) {
        // Si aucun panier n'existe pour l'utilisateur, créez-en un
        $cart = new Cart();
        $cart->setUser($user);
        $entityManager->persist($cart);
        $entityManager->flush();
    }

    $total = 0;
    foreach ($cart->getItems() as $item) {
        $total += $item->getProduct()->getPrice() * $item->getQuantity();
    }

    return $this->render('cart/index.html.twig', [
        'cart' => $cart,
        'total' => $total,
    ]);
}

    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $cart = $this->getUser()->getCart();
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($this->getUser());
            $entityManager->persist($cart);
        }

        $size = $request->request->get('size');
        $quantity = $request->request->get('quantity', 1);

        $cartItem = $entityManager->getRepository(CartItem::class)->findOneBy([
            'cart' => $cart,
            'product' => $product,
            'size' => $size,
        ]);



        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setProduct($product);
            $cartItem->setSize($size);
            $cartItem->setQuantity($quantity);
            $entityManager->persist($cartItem);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{productId}', name: 'cart_remove')]
    public function removeProduct(int $productId, CartRepository $cartRepository, ProductRepository $productRepository): RedirectResponse
    {
        // Assurez-vous que l'utilisateur est authentifié
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Rediriger si l'utilisateur n'est pas connecté
        }

        // Récupérer le panier de l'utilisateur
        $cart = $user->getCart();
        $product = $productRepository->find($productId);

        if ($product) {
            // Supprimer le produit du panier
            $cart->removeProduct($product);

            // Sauvegarder les changements dans la base de données
            $cartRepository->save($cart, true);
        }

        // Rediriger vers la page du panier
        return $this->redirectToRoute('app_cart');
    }
}
