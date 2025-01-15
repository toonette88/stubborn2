<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\Product;

class CartTest extends WebTestCase
{
    public function testCartPage(): void
{
    $client = static::createClient();

    // Accéder à la page de connexion
    $crawler = $client->request('GET', '/login');

    // Soumettre le formulaire avec des informations valides
    $form = $crawler->selectButton('Se Connecter')->form([
        'name' => 'user1', // Un utilisateur valide depuis vos fixtures
        'password' => 'pass_1234',   // Le mot de passe valide correspondant
    ]);
    $client->submit($form);

    // Accéder à la page du panier
    $client->request('GET', '/cart');

    // Debug : afficher le contenu de la page pour vérifier ce qui est rendu
    $content = $client->getResponse()->getContent();
    echo $content;

    // Vérifier qu'aucune redirection ne s'est produite
    $this->assertFalse($client->getResponse()->isRedirect(), 'Unexpected redirection occurred.');

    // Vérifier que la réponse est réussie
    $this->assertResponseIsSuccessful();

    // Vérifier que le titre attendu est présent
    $this->assertSelectorTextContains('h1', 'Mon Panier');
}

    

    public function testAddProductToCart(): void
    {
        $client = static::createClient();

        // Étape 1 : Se connecter
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'name' => 'user1',
            'password' => 'pass_1234',
        ]);
        $client->submit($form);

        // Vérifier que la connexion est réussie
        $this->assertResponseRedirects('/');
        $client->followRedirect();
        $this->assertSelectorExists('.navbar .nav-link', 'Se déconnecter'); // Vérifie que l'utilisateur est connecté

        // Étape 2 : Vérifier l'existence du produit
        $productRepository = static::getContainer()->get('doctrine')->getRepository(Product::class);
        $product = $productRepository->findOneBy(['name' => 'Blackbelt']);
        $this->assertNotNull($product, 'Le produit "Blackbelt" n’existe pas dans les fixtures.');

        // Étape 3 : Ajouter le produit au panier
        $client->request('POST', '/cart/add/' . $product->getId(), [
            'size' => 'M',
            'quantity' => 1,
        ]);

        // Vérifier la redirection vers la page du panier
        $this->assertResponseRedirects('/cart');
        $client->followRedirect();

        // Étape 4 : Vérifier que le panier contient le produit
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.cart-items');
        $this->assertSelectorTextContains('.cart-items', 'Blackbelt');
        $this->assertSelectorTextContains('.cart-items', 'M'); // Vérifie la taille
    }

    public function testRemoveProductFromCart(): void
{
    $client = static::createClient();

    // Étape 1 : Se connecter
    $crawler = $client->request('GET', '/login');
    $form = $crawler->selectButton('Se Connecter')->form([
        'name' => 'user1', // Remplacez par un utilisateur valide de vos fixtures
        'password' => 'pass_1234', // Mot de passe valide
    ]);
    $client->submit($form);

    // Étape 2 : Ajouter un produit au panier
    $productRepository = static::getContainer()->get('doctrine')->getRepository(Product::class);
    $product = $productRepository->findOneBy(['name' => 'Blackbelt']); // Produit des fixtures
    $this->assertNotNull($product, 'Product "Blackbelt" not found in fixtures.');

    $client->request('POST', '/cart/add/' . $product->getId(), [
        'size' => 'M',
        'quantity' => 1,
    ]);

    // Suivre la redirection vers le panier
    $this->assertResponseRedirects('/cart');
    $client->followRedirect();

    // Vérifier que le produit est bien dans le panier
    $this->assertSelectorTextContains('.cart-items', 'Blackbelt');

    // Étape 3 : Retirer l'article du panier
    $cartRepository = static::getContainer()->get('doctrine')->getRepository(\App\Entity\CartItem::class);
    $cartItem = $cartRepository->findOneBy(['product' => $product]);
    $this->assertNotNull($cartItem, 'Cart item for "Blackbelt" not found.');

    $client->request('GET', '/cart/remove/' . $cartItem->getId());
    
    // Suivre la redirection après le retrait
    $this->assertResponseRedirects('/cart');
    $client->followRedirect();

    // Vérifier que le panier est vide
    $this->assertSelectorTextContains('p', 'Votre panier est vide.');
}


}
