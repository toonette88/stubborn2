<?php

namespace App\Controller;

use App\Service\StripePaymentService;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
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
        // Récupérer l'utilisateur et son panier
        $user = $this->getUser();
        $cart = $user?->getCart();

        if (!$cart || empty($cart->getItems())) {
            // Rediriger vers la page du panier si le panier est vide ou inexistant
            return $this->redirectToRoute('cart');
        }

        // Utiliser CartService pour calculer le total
        $totalAmount = $this->cartService->calculateTotal($cart);

        if ($totalAmount <= 0) {
            // Si le montant total est invalide, afficher un message d'erreur
            return $this->render('payment/error.html.twig', [
                'error' => 'Le montant total de votre panier est invalide.',
            ]);
        }

        try {
            $successUrl = $this->urlGenerator->generate('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $cancelUrl = $this->urlGenerator->generate('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

            // Créer une session de paiement Stripe via le service
            $checkoutSession = $this->stripePaymentService->createCheckoutSession($totalAmount, $successUrl, $cancelUrl);

            // Rediriger vers la session Stripe
            return $this->redirect($checkoutSession->url, 303);
        } catch (\Exception $e) {
            return $this->render('payment/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $cart = $user?->getCart();

        if (!$cart) {
            return $this->redirectToRoute('cart');
        }

        // Vider le panier
        foreach ($cart->getItems() as $item) {
            $cart->removeItem($item);
        }

        // Sauvegarder les modifications en base de données
        $entityManager->flush();

        // Afficher la page de succès
        return $this->render('payment/success.html.twig');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }
}
