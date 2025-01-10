<?php

use PHPUnit\Framework\TestCase;
use App\Service\StripePaymentService;

class StripePaymentServiceTest extends TestCase
{
    public function testCreateCheckoutSession()
    {
        $stripeServiceMock = $this->createMock(StripePaymentService::class);
        $stripeServiceMock
            ->method('createCheckoutSession')
            ->willReturn(['url' => 'https://checkout.stripe.com/session_id']);

    }
}
