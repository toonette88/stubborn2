<?php

namespace App\Controller;

use App\Service\StripeClient;
use App\Service\StripePaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private StripePaymentService $stripePaymentService;

    public function __construct(StripePaymentService $stripePaymentService, StripeClient $stripeClient)
    {
        $this->stripePaymentService = $stripePaymentService;
        // StripeClient initialise le client Stripe avec la clé secrète
    }

    #[Route('/payment', name: 'app_payment')]
    public function payment(): Response
    {
        // Récupérer le panier de l'utilisateur connecté
        $cart = $this->getUser()->getCart();
        $totalAmount = 0;

        // Calculer le montant total du panier
        foreach ($cart->getItems() as $item) {
            $totalAmount += $item->getProduct()->getPrice() * $item->getQuantity();
        }

        try {
            // Créer une session de paiement Stripe via le service
            $checkoutSession = $this->stripePaymentService->createCheckoutSession($totalAmount);

            // Rediriger vers la session Stripe
            return $this->redirect($checkoutSession->url);
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
        $cart = $user->getCart();

        // Vider le panier
        foreach ($cart->getItems() as $item) {
            $cart->removeItem($item);
        }

        $entityManager->flush();

        return $this->render('payment/success.html.twig');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }
}
