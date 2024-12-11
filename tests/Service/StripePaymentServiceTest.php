<?php

namespace App\Tests\Service;

use App\Service\StripePaymentService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\StripeClient;
use Stripe\Checkout\Session;

class StripePaymentServiceTest extends TestCase
{
    public function testCreateCheckoutSession(): void
    {
        // Créer un mock de StripeClient
        $stripeClientMock = $this->createMock(StripeClient::class);

        // Créer un mock pour la réponse de la session Stripe
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->id = 'fake_session_id';

        // Simuler le comportement de la méthode checkout->sessions->create()
        $stripeClientMock->method('__call')
            ->with('checkout.sessions.create', $this->anything())
            ->willReturn($sessionMock);

        // Créer un mock de UrlGeneratorInterface
        $urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);
        $urlGeneratorMock->method('generate')
            ->willReturn('http://example.com/success');

        // Créer une instance de StripePaymentService avec le mock de StripeClient
        $stripeService = new StripePaymentService($stripeClientMock, $urlGeneratorMock);

        // Appeler la méthode à tester
        $session = $stripeService->createCheckoutSession(1000, 'http://example.com/success', 'http://example.com/cancel');

        // Vérifier que le résultat est une instance de Session
        $this->assertInstanceOf(Session::class, $session);
        $this->assertEquals('fake_session_id', $session->id);
    }
}
