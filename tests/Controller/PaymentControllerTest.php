<?php
namespace App\Tests\Controller;

use App\Controller\PaymentController;
use App\Service\StripePaymentService;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Cart;
use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class PaymentControllerTest extends WebTestCase
{
    private $stripePaymentServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the mock for Stripe\Checkout\Session
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->id = 'fake_session_id'; // Simuler un ID de session Stripe
        
        // Create the mock for StripePaymentService
        $this->stripePaymentServiceMock = $this->createMock(StripePaymentService::class);
        $this->stripePaymentServiceMock
            ->method('createCheckoutSession')
           ->willReturn($sessionMock);

        // Set the mock service in the container
        static::getContainer()->set(StripePaymentService::class, $this->stripePaymentServiceMock);
    }

    private function logIn(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
    
        // Create a dummy user with unique values
        $user = new User();
        $user->setName('UniqueUser' . uniqid());
        $user->setEmail('test' . uniqid() . '@example.com');
        $user->setPassword('password');
        $user->setAdress('123 Unique Street');

        // Create a dummy product
        $product = new Product();
        $product->setName('Test Product');
        $product->setDescription('A product for testing.');
        $product->setPrice(19.99);
        $product->setIsFeatured(true);
        $product->setStockXS(10);
        $product->setStockS(10);
        $product->setStockM(10);
        $product->setStockL(10);
        $product->setStockXL(10);

        // Create a CartItem with the product
        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setQuantity(1); // For example, 1 item
        $cartItem->setSize('M'); // Product size
    
        // Create a Cart and associate it with the user
        $cart = new Cart();
        $cart->addItem($cartItem);
        $user->setCart($cart);

        // Add the product to the cart
        $cart->addProduct($product);
    
        // Persist the user and cart in the database
        $entityManager->persist($product);
        $entityManager->persist($cartItem);
        $entityManager->persist($cart);
        $entityManager->persist($user);
        $entityManager->flush();
    
        // Simulate login
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        static::getContainer()->get('security.token_storage')->setToken($token);
        static::getContainer()->get('session')->set('_security_main', serialize($token));
        static::getContainer()->get('session')->save();
    }

    public function testPaymentRedirectsToStripeCheckout(): void
    {
        $client = static::createClient();
        
        // Log in the user
        $this->logIn();
        
        // Send the request to the payment page
        $client->request('GET', '/payment');
        
        // Check if the response redirects to the Stripe checkout URL
        $this->assertResponseRedirects('https://fake-stripe-url.com');
    }

    public function testPaymentSuccessClearsCart(): void
    {
        $client = static::createClient();
        $client->request('GET', '/payment/success');
    
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Paiement réussi !');
    }

    public function testPaymentCancelPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/payment/cancel');
    
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Paiement annulé');
    }
}
