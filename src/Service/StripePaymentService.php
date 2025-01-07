<?php

namespace App\Service;

use Stripe\StripeClient;

class StripePaymentService
{
    private StripeClient $stripeClient;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripeClient = new StripeClient($stripeSecretKey);
    }

    /**
     * Crée une session de paiement Stripe.
     *
     * @param array $items Un tableau d'articles pour le paiement
     * @param string $successUrl URL de redirection après un paiement réussi
     * @param string $cancelUrl URL de redirection après une annulation
     * @return array Données pertinentes de la session de paiement
     */
    public function makePayment(array $items, string $successUrl, string $cancelUrl): array
    {
        // Formattez les articles pour Stripe
        $lineItems = array_map(function ($item) {
            return [
                'price_data' => [
                    'currency' => $item['currency'], // e.g., 'usd' or 'eur'
                    'product_data' => [
                        'name' => $item['name'], // Nom du produit
                    ],
                    'unit_amount' => $item['price'] * 100, // Prix en centimes
                ],
                'quantity' => $item['quantity'],
            ];
        }, $items);

        // Créez une session de paiement Stripe
        $session = $this->stripeClient->checkout->sessions->create([
            'payment_method_types' => ['card'], // Méthodes de paiement acceptées
            'line_items' => $lineItems, // Articles à facturer
            'mode' => 'payment', // Mode de paiement unique
            'success_url' => $successUrl, // URL de succès
            'cancel_url' => $cancelUrl, // URL d'annulation
        ]);

        // Retourne les données pertinentes de la session
        return [
            'id' => $session->id,
            'url' => $session->url,
        ];
    }

    public function setStripeClient(StripeClient $stripeClient): void
    {
        $this->stripeClient = $stripeClient;
    }
}

