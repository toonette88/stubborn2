<?php

namespace App\Tests\Service;

use App\Service\StripePaymentService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePaymentServiceTest extends TestCase
{
    public function testCreateCheckoutSession(): void
    {
        // Configurer une clé API de test pour Stripe
        Stripe::setApiKey('sk_test_51QRhxcJxoR8xsc2FubXoql05icoQtpvrveOP3Q2smOb58W3NQ198FGt6A10OzveCpO3qO1KCHBJfoXB5IcEZPdPA00aXhpFhL6');

        // Créer un mock de UrlGeneratorInterface
        $urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        // Simuler le comportement de generate pour les URLs de succès et d'annulation
        $urlGeneratorMock->method('generate')
            ->willReturn('http://example.com/success');

        // Créer une instance de StripePaymentService avec le mock
        $stripeService = new StripePaymentService($urlGeneratorMock, 'sk_test_your_test_key_here');

        // Appeler la méthode à tester
        $session = $stripeService->createCheckoutSession(1000, 'http://example.com/success', 'http://example.com/cancel');

        // Vérifier que le résultat est une instance de Session
        $this->assertInstanceOf(Session::class, $session);
        $this->assertNotEmpty($session->id);
    }
}
