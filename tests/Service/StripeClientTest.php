<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use Zenstruck\Foundry\Test\Factories;
use App\Service\StripeClient;
use Stripe\Checkout\Session;

class StripeClientTest extends TestCase
{
    use Factories; 

    public function testCreateCheckoutSession(): void
    {
        $stripeSecretKey = 'sk_test_12345';
        $totalAmount = 50.00;
        $successUrl = 'https://example.com/success';
        $cancelUrl = 'https://example.com/cancel';
    
        // Create mock for Session
        $mockSession = $this->createMock(Session::class);
        $mockSession->id = 'test_session_id';
        $mockSession->url = 'https://example.com/checkout';
    
        // Create mock for StripeClient
        $stripeClient = $this->createMock(StripeClient::class);
        $stripeClient->method('createCheckoutSession')
            ->willReturn($mockSession);
    
        // Call the method on the mock
        $result = $stripeClient->createCheckoutSession($totalAmount, $successUrl, $cancelUrl);
    
        // Assertions
        $this->assertEquals('test_session_id', $result->id);
        $this->assertEquals('https://example.com/checkout', $result->url);
    }
}
