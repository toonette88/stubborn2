<?php

namespace App\Tests\Service;

use App\Service\StripeClient;
use App\Service\StripePaymentService;
use PHPUnit\Framework\TestCase;
use Stripe\Checkout\Session;

class StripePaymentServiceTest extends TestCase
{
    public function testMakePayment(): void
    {
        // Mock items for payment
        $items = [
            [
                'name' => 'Test Item',
                'price' => 5000,
                'quantity' => 1,
                'currency' => 'eur',
            ],
        ];
        $successUrl = 'https://example.com/success';
        $cancelUrl = 'https://example.com/cancel';

        // Mock Session
        $mockSession = $this->createMock(Session::class);
        $mockSession->id = 'test_session_id';
        $mockSession->url = 'https://example.com/checkout';

        // Create mock for StripeClient
        $stripeClient = $this->createMock(StripeClient::class);
        $stripeClient->method('createCheckoutSession')
            ->willReturn($mockSession);

        // Inject the API key directly in the test
        $stripeApiKey = 'sk_test_12345';

        // Manually inject the mock StripeClient into StripePaymentService
        $stripeService = new StripePaymentService($stripeApiKey);
        $stripeService->setStripeClient($stripeClient);

        // Call the method to test payment
        $result = $stripeService->makePayment($items, $successUrl, $cancelUrl);

        // Assertions
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertEquals('test_session_id', $result['id']);
        $this->assertEquals('https://example.com/checkout', $result['url']);
    }
}
