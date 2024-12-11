<?php
namespace App\Service;

use Stripe\Checkout\Session;

class StripePaymentService
{
    private $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function createCheckoutSession(float $totalAmount, string $successUrl, string $cancelUrl): Session
    {
        // Crée une session de paiement Stripe
        $session = $this->stripeClient->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Panier',
                        ],
                        'unit_amount' => (int) ($totalAmount * 100), // Montant en centimes
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        return $session;
    }
}
