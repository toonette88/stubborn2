<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\AppFixtures;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\UserRepository;

class CartTest extends WebTestCase
{
    public function testCartPageWithFixtures(): void
    {
        $client = static::createClient();

        // Récupérer un utilisateur à partir des fixtures
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByName('user1');

        // Simulation de l'authentification
        $client->loginUser($user);

        // Tester l'accès au panier
        $client->request('GET', '/cart');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1','Mon panier');
       // $this->assertSelectorExists('.cart-items'); // Vérifie si la liste des articles est présente
    }
}
