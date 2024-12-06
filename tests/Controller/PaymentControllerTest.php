<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;

class PaymentControllerTest extends WebTestCase
{
    public function testPaymentPageIsAccessible(): void
    {
        $client = static::createClient();
    
        // Générer un nom unique pour éviter les conflits d'unicité
        $uniqueName = 'TestUser_' . uniqid();
    
        // Créez un produit réel avec un prix
        $product = (new Product())
            ->setPrice(100)
            ->setName('Test Product')
            ->setDescription('Product for testing')
            ->setImage('product.jpg')
            ->setFeatured(true)
            ->setStockXS(10)
            ->setStockS(20)
            ->setStockM(30)
            ->setStockL(40)
            ->setStockXL(50);
    
        // Créez un article de panier réel
        $cartItem = (new CartItem())
            ->setProduct($product)
            ->setQuantity(2)
            ->setSize('M');
    
        // Créez un panier avec des articles réels
        $cart = new Cart();
        $cart->addItem($cartItem);
    
        // Créez un utilisateur réel et assignez-lui le panier
        $user = (new User())
            ->setEmail('test@example.com')
            ->setName($uniqueName)
            ->setAdress('123 Test St')
            ->setCart($cart);
    
        // Simulez la connexion de l'utilisateur
        $client->loginUser($user);
    
        // Vérification que le panier est correctement initialisé avant la requête
        $this->assertNotNull($user->getCart(), 'Le panier de l\'utilisateur ne doit pas être nul.');
    
        // Requête vers la page de paiement
        $client->request('GET', '/payment');
    
        // Vérification de la réponse de la page
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
    
    

    public function testPaymentSuccess(): void
    {
        $client = static::createClient();

        // Créez un utilisateur réel pour la connexion
        $user = $this->createMockUser();

        // Faites une requête à la route de succès
        $client->request('GET', '/payment/success');

        // Vérifiez que la page de succès est affichée
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Merci pour votre paiement');
    }

    public function testStripeSessionIsCreated(): void
    {
        $client = static::createClient();

        // Créez un utilisateur réel pour la connexion
        $user = $this->createMockUser();

        // Mock Stripe (par exemple, pour ne pas appeler Stripe réellement)
        $stripeMock = $this->getMockBuilder(\Stripe\Checkout\Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        // Simulez une URL Stripe pour la redirection
        $stripeMock->url = 'https://checkout.stripe.com/test-session';

        // Remplacer le comportement de `Session::create`
        $stripeMock::staticExpects($this->once())
            ->method('create')
            ->willReturn($stripeMock);

        // Testez la route de paiement
        $client->request('GET', '/payment');

        // Vérifiez que la redirection vers Stripe se produit correctement
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $targetUrl = $client->getResponse()->headers->get('Location');
        $this->assertSame('https://checkout.stripe.com/test-session', $targetUrl);
    }

    private function createMockUser(): User
{
    // Générer un nom unique pour éviter les conflits d'unicité
    $uniqueName = 'TestUser_' . uniqid();

    // Créez un produit réel
    $product = (new Product())
        ->setPrice(50)
        ->setName('Test Product 2')
        ->setDescription('Another product')
        ->setImage('product2.jpg')
        ->setIsFeatured(true)
        ->setStockXS(10)
        ->setStockS(20)
        ->setStockM(30)
        ->setStockL(40)
        ->setStockXL(50);

    // Créez un article de panier
    $cartItem = (new CartItem())
        ->setProduct($product)
        ->setQuantity(1)
        ->setSize('S');

    // Créez un panier
    $cart = new Cart();
    $cart->addItem($cartItem);

    // Créez un utilisateur avec un nom unique
    $user = (new User())
        ->setEmail('testuser@example.com')
        ->setName($uniqueName)  // Utiliser le nom unique généré
        ->setAdress('123 Test St')
        ->setCart($cart);

    return $user;
}

}
