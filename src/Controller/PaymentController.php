<?php

namespace App\Controller;

use App\Service\StripePaymentService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CartService;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    private StripePaymentService $stripePaymentService;
    private CartService $cartService;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        StripePaymentService $stripePaymentService,
        CartService $cartService,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->stripePaymentService = $stripePaymentService;
        $this->cartService = $cartService;
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/payment', name: 'app_payment')]
    public function payment(): Response
    {
        $user = $this->getUser();
        $cart = $user?->getCart();

        if (!$cart || empty($cart->getItems())) {
            return $this->redirectToRoute('app_cart');
        }

        $items = [];
        foreach ($cart->getItems() as $item) {
            $items[] = [
                'name' => $item->getProduct()->getName(),
                'price' => $item->getProduct()->getPrice(),
                'quantity' => $item->getQuantity(),
                'currency' => 'eur', 
            ];
        }

        $successUrl = $this->urlGenerator->generate('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelUrl = $this->urlGenerator->generate('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

        try {
            $checkoutSession = $this->stripePaymentService->createCheckoutSession($items, $successUrl, $cancelUrl);

            return $this->redirect($checkoutSession['url'], 303);
        } catch (\Exception $e) {
            return $this->render('payment/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

     // Route appelée en cas de paiement réussi
     #[Route('/payment/success', name: 'payment_success')]
     public function paymentSuccess(EntityManagerInterface $entityManager): Response
     {
         // Vider le panier après un paiement réussi
         $user = $this->getUser();
         $cart = $user->getCart();
 
         // Effacer les éléments du panier
         foreach ($cart->getItems() as $item) {
             $cart->removeItem($item);  // Supposons que vous avez une méthode removeItem
         }
 
         // Sauvegarder le panier vide
         $entityManager->flush();
 
         // Renvoyer une réponse de succès
         return $this->render('payment/success.html.twig');
     }
 
     // Route appelée en cas de paiement annulé
     #[Route('/payment/cancel', name: 'payment_cancel')]
     public function paymentCancel(): Response
     {
         return $this->render('payment/cancel.html.twig');
     }
}
