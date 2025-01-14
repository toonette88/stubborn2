<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\Product;

class CartTest extends WebTestCase
{
    public function testCartPageWithFixtures(): void
    {
        $client = static::createClient();

        // Récupérer un utilisateur
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['name' => 'user1']);
        $this->assertNotNull($user, 'User "user1" not found in fixtures.');

        // Simuler l'authentification
        $client->loginUser($user);

        // Accéder à la page du panier
        $client->request('GET', '/cart');

        // Suivre une redirection si elle existe
        if ($client->getResponse()->isRedirect()) {
            $client->followRedirect();
            $this->assertSelectorTextContains('h1', 'Se connecter');
        } else {
            // Vérifier le contenu de la page si pas de redirection
            $this->assertResponseIsSuccessful();
            $this->assertSelectorTextContains('h1', 'Mon panier');
        }
    }

    public function testAddProductToCart(): void
    {
        $client = static::createClient();
        
        // Authentifier un utilisateur
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['name' => 'user1']);
        $this->assertNotNull($user, 'User "user1" not found in fixtures.');

        // Simuler l'authentification
        $client->loginUser($user);

        // Vérifier que l'utilisateur est authentifié (redirection vers /cart si ok)
        $client->request('GET', '/cart');
        $this->assertResponseIsSuccessful(); // Vérifier que l'utilisateur est bien redirigé vers la page panier
        $this->assertSelectorTextContains('h1', 'Mon panier');
        
        // Récupérer un produit
        $productRepository = static::getContainer()->get('doctrine')->getRepository(Product::class);
        $product = $productRepository->findOneBy(['name' => 'Blackbelt']); // Produit des fixtures
        $this->assertNotNull($product, 'Product "Blackbelt" not found in fixtures.');

        // Envoyer une requête POST pour ajouter le produit au panier
        $client->request('POST', '/cart/add/' . $product->getId(), [
            'size' => 'M',
            'quantity' => 1,
        ]);
        
        // Vérifier la redirection vers la page du panier
        $this->assertResponseRedirects('/cart');
        $client->followRedirect();
        
        // Vérifier que la page du panier contient les informations du produit
        $this->assertSelectorTextContains('.cart-items', 'Blackbelt');
    }

    

}
