<?php

namespace App\Service;

use Stripe\Stripe;

class StripeClient
{
    public function __construct(string $stripeSecretKey)
    {
        Stripe::setApiKey($stripeSecretKey);
    }
}