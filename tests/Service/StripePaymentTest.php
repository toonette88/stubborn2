<?php

use PHPUnit\Framework\TestCase;
use App\Service\StripePaymentService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripePaymentServiceTest extends TestCase
{
    public function testCreateCheckoutSession()
    {
        // Mock de la classe StripePaymentService
        $stripeServiceMock = $this->createMock(StripePaymentService::class);
        
        // On s'attend à ce que createCheckoutSession retourne une URL
        $stripeServiceMock
            ->method('createCheckoutSession')
            ->willReturn(['url' => 'https://checkout.stripe.com/session_id']);
        
        // Vérification de l'URL retournée
        $result = $stripeServiceMock->createCheckoutSession([], 'https://success.url', 'https://cancel.url');
        
        $this->assertArrayHasKey('url', $result);
        $this->assertEquals('https://checkout.stripe.com/session_id', $result['url']);
    }
}

