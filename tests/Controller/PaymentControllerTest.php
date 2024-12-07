<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;


class PaymentControllerTest extends WebTestCase
{
    public function testPaymentPageRedirectsToStripe(): void
{
    $client = static::createClient();

    // Créer un utilisateur fictif
    $user = $this->createMockUser();

    // Simuler la connexion de l'utilisateur
    $client->loginUser($user);

    // Effectuer la requête vers la route de paiement
    $client->request('GET', '/payment');

    // Vérifier que la réponse est une redirection vers Stripe
    $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    $this->assertStringContainsString('https://checkout.stripe.com/', $client->getResponse()->headers->get('Location'));
}
private function createMockUser(): User
{
    $uniqueName = 'TestUser_' . uniqid();

    // Créez un produit pour le panier
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

    // Créez un article de panier
    $cartItem = (new CartItem())
        ->setProduct($product)
        ->setQuantity(2)
        ->setSize('M');

    // Créez un panier et ajoutez l'article
    $cart = new Cart();
    $cart->addItem($cartItem);

    // Créez un utilisateur et assignez le panier
    $user = (new User())
        ->setEmail('test@example.com')
        ->setName($uniqueName)
        ->setAdress('123 Test St')
        ->setCart($cart);

    // S'assurer que le panier est bien associé à l'utilisateur
    $cart->setUser($user);

    return $user;
}

}
