<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\Product;

class PaymentTest extends WebTestCase
{
    public function testPurchaseWithStripe(): void
    {
        $client = static::createClient();
        
        // Étape 1 : Se connecter
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'name' => 'user1',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
        
        // Étape 2 : Ajouter un produit au panier
        $productRepository = static::getContainer()->get('doctrine')->getRepository(Product::class);
        $product = $productRepository->findOneBy(['name' => 'Blackbelt']);
        $this->assertNotNull($product, 'Le produit "Blackbelt" n’existe pas dans les fixtures.');
    
        $client->request('POST', '/cart/add/' . $product->getId(), [
            'size' => 'M',
            'quantity' => 1,
        ]);
    
        $this->assertResponseRedirects('/cart');
        $crawler = $client->followRedirect();
    
        $this->assertSelectorTextContains('.cart-items', 'Blackbelt');
    
        // Étape 3 : Vérifier et soumettre le formulaire de paiement
        $form = $crawler->selectButton('Finaliser ma commande')->form();
        $client->submit($form);
    
        $response = $client->getResponse();
    
        // Vérifiez la redirection vers Stripe
        $this->assertSame(303, $response->getStatusCode(), 'Expected status code 303 for redirection to Stripe');
        $this->assertStringStartsWith('https://checkout.stripe.com/c/pay/', $response->headers->get('Location'));
    
        // Étape 4 : Simuler un webhook Stripe pour valider le paiement
        $stripeEvent = [
            'id' => 'evt_test_123',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_a1Wu4Jtz6ul7bNcTLOjaNCMa33xSWne0X13rSacISJTWrEyvFaCu3eti0D',
                    'amount_total' => 5000,
                    'currency' => 'usd',
                    'payment_status' => 'paid',
                ],
            ],
        ];
    
        $client->request('POST', '/webhook/stripe', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($stripeEvent));
    
        // Étape 5 : Vérifier la page de succès
        $client->request('GET', '/payment/success');
        $this->assertSelectorTextContains('h1', 'Paiement réussi !');
    
    }
    
    

    public function testPurchaseWithStripeNoItemsInCart(): void
    {
        $client = static::createClient();
        
        // Se connecter
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'name' => 'user1',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);
        
        // Tentative de paiement sans article dans le panier
        $client->request('GET', '/payment');
        
        // Vérifier qu'il est redirigé vers le panier
        $this->assertResponseRedirects('/cart');
    }

}