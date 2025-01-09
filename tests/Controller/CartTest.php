<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Product;

class CartTest extends WebTestCase
{
    public function testAddProductToCart(): void
    {
        $client = static::createClient();

         // Charger les fixtures
         $container = static::getContainer();
         $productRepository = $container->get('doctrine')->getRepository(Product::class);
 
         // Utilisez le repository pour récupérer le produit
         $product = $productRepository->findOneBy(['name' => 'Blackbelt']);
         $this->assertNotNull($product, 'Le produit "Blackbelt" n\'a pas été trouvé.');;

        //Accéder à la page d'un produit
        $crawler = $client->request('GET', '/product/'. $product->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('p', 'Nom: Blackbelt');

        // Soumettre le formulaire d'ajout au panier
        $form = $crawler->selectButton('AJOUTER AU PANIER')->form();
        $client->submit($form);

        //Vérifier que la réponse est une redirection vers le panier
        $this->assertResponseRedirects('/cart');

        $client->followRedirect();

        // Vérifier que le produit est affiché dans le panier
        $this->assertSelectorTextContains('.cart-item', 'Blackbelt');



    }
}
