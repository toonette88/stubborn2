<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cart')]
class CartController extends AbstractController
{
    public function __construct(private CartService $cartService) {}

    #[Route('/', name: 'app_cart')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $this->cartService->getOrCreateCart($user);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'total' => $cart->calculateTotal(),
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(Product $product, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $size = $request->request->get('size');
        $quantity = max(1, (int) $request->request->get('quantity', 1));

        try {
            $this->cartService->addProductToCart($user, $product, $size, $quantity);
            $this->addFlash('success', 'Produit ajouté au panier.');
        } catch (\LogicException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        try {
            $this->cartService->removeProductFromCart($user, $id);
            $this->addFlash('success', 'Produit retiré du panier.');
        } catch (\LogicException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_cart');
    }
}
