<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
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

        // Récupérer ou créer le panier de l'utilisateur
        $cart = $this->cartService->getOrCreateCart($user);

        // Calculer le total
        $total = $this->cartService->calculateTotal($cart);

        return $this->render('cart/index.html.twig', [
            'items' => $cart->getItems(),
            'total' => $total, // Transmet explicitement le total à la vue
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(Product $product, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer la taille et la quantité
        $size = $request->request->get('size');
        $quantity = max(1, (int) $request->request->get('quantity', 1));

        try {
            // Ajouter le produit au panier
            $this->cartService->addProductToCart($user, $product, $size, $quantity);
            $this->addFlash('success', 'Produit ajouté au panier.');
        } catch (\LogicException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur inattendue est survenue.');
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
            // Retirer le produit du panier
            $this->cartService->removeProductFromCart($user, $id);
            $this->addFlash('success', 'Produit retiré du panier.');
        } catch (\LogicException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur inattendue est survenue.');
        }

        return $this->redirectToRoute('app_cart');
    }
}
