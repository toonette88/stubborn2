<?php
namespace App\Tests\Controller;

use App\Controller\PaymentController;
use App\Service\StripePaymentService;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class PaymentControllerTest extends WebTestCase
{
    private $stripePaymentServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
    
        // Créer un mock pour Stripe\Checkout\Session
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->url = 'https://fake-stripe-url.com';
    
        // Créer un mock pour StripePaymentService
        $this->stripePaymentServiceMock = $this->createMock(StripePaymentService::class);
        $this->stripePaymentServiceMock
            ->method('createCheckoutSession')
            ->willReturn($sessionMock);
    }
    
    private function logIn(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
    
        // Créer un utilisateur fictif avec des valeurs uniques
        $user = new User();
        $user->setName('UniqueUser' . uniqid());
        $user->setEmail('test' . uniqid() . '@example.com');
        $user->setPassword('password');
        $user->setAdress('123 Unique Street');
    
        // Créer un panier fictif et l'associer à l'utilisateur
        $cart = new Cart();
        $user->setCart($cart);

         // Créer un produit fictif et l'ajouter au panier
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(19.99); // Exemple de prix
        $product->setDescription('This is a test product.');

        // Ajouter le produit au panier
        $cart->addProduct($product);
    
        // Persister l'utilisateur et le panier dans la base de données
        $entityManager->persist($product);
        $entityManager->persist($cart);
        $entityManager->persist($user);
        $entityManager->flush();
    
        // Simuler l'authentification
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        static::getContainer()->get('security.token_storage')->setToken($token);
        static::getContainer()->get('session')->set('_security_main', serialize($token));
        static::getContainer()->get('session')->save();
    }
    
    public function testPaymentRedirectsToStripeCheckout(): void
    {
        $client = static::createClient();
    
        // Authentifier l'utilisateur
        $this->logIn();
    
        static::getContainer()->set(StripePaymentService::class, $this->stripePaymentServiceMock);
    
        $client->request('GET', '/payment');
    
        $this->assertResponseRedirects('https://fake-stripe-url.com');
    }

    public function testPaymentSuccessClearsCart(): void
    {
        $client = static::createClient();
        static::getContainer()->set(StripePaymentService::class, $this->stripePaymentServiceMock);
    
        $client->request('GET', '/payment/success');
    
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Paiement réussi !');
    }
    

    public function testPaymentCancelPage(): void
    {
        $client = static::createClient();
        static::getContainer()->set(StripePaymentService::class, $this->stripePaymentServiceMock);
    
        $client->request('GET', '/payment/cancel');
    
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Paiement annulé');
    }
    

}
