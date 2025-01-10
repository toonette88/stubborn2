<?php

namespace App\Service;

use Stripe\StripeClient;

class StripePaymentService
{
    private StripeClient $stripeClient;

    public function __construct(string $stripeSecretKey)
    {
        // Initialiser le client Stripe
        $this->stripeClient = new StripeClient($stripeSecretKey);
    }

    /**
     * Crée une session de paiement Stripe.
     *
     * @param array $items Un tableau d'articles pour le paiement
     * @param string $successUrl URL de redirection après un paiement réussi
     * @param string $cancelUrl URL de redirection après une annulation
     * @return array Données pertinentes de la session Stripe
     * @throws \RuntimeException Si une erreur se produit
     */
    public function createCheckoutSession(array $items, string $successUrl, string $cancelUrl): array
    {
        if (empty($items)) {
            throw new \LogicException('La liste des articles ne peut pas être vide.');
        }

        $lineItems = array_map(function ($item) {
            if (!isset($item['name'], $item['price'], $item['quantity'], $item['currency'])) {
                throw new \LogicException('Chaque article doit inclure "name", "price", "quantity" et "currency".');
            }

            return [
                'price_data' => [
                    'currency' => $item['currency'],
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => $item['price'] * 100, // Prix en centimes
                ],
                'quantity' => $item['quantity'],
            ];
        }, $items);

        try {
            $session = $this->stripeClient->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

            return [
                'id' => $session->id,
                'url' => $session->url,
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la création de la session Stripe : ' . $e->getMessage());
        }
    }
}
