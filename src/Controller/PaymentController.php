<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    // Route pour afficher la page de paiement
    #[Route('/payment', name: 'app_payment')]
    public function payment(Request $request): Response
    {
        // Récupérer le panier de l'utilisateur connecté
        $cart = $this->getUser()->getCart(); // Assurez-vous que l'utilisateur est connecté
        $totalAmount = 0;

        // Calculer le montant total du panier
        foreach ($cart->getItems() as $item) {
            $totalAmount += $item->getProduct()->getPrice() * $item->getQuantity();
        }

        // Initialiser Stripe avec la clé secrète
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']); // Assurez-vous que la clé API est correctement définie dans .env

        try {
            // Créer une session de paiement Stripe
            $checkoutSession = Session::create([
                'payment_method_types' => ['card'], // Types de méthodes de paiement autorisées
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'eur', // Devise utilisée
                            'product_data' => [
                                'name' => 'Votre panier', // Nom du produit (peut être plus détaillé)
                            ],
                            'unit_amount' => $totalAmount * 100, // Montant total en centimes (Stripe prend en centimes)
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment', // Mode de la session de paiement (ici pour un paiement unique)
                'success_url' => $this->generateUrl('payment_success', [], 0), // URL de succès
                'cancel_url' => $this->generateUrl('payment_cancel', [], 0), // URL d'annulation
            ]);

            // Rediriger vers la session Stripe
            return $this->redirect($checkoutSession->url);
        } catch (\Exception $e) {
            // Si une erreur se produit, afficher un message d'erreur
            return $this->render('payment/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    // Route appelée en cas de paiement réussi
    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(): Response
    {
        return $this->render('payment/success.html.twig');
    }

    // Route appelée en cas de paiement annulé
    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }
}
