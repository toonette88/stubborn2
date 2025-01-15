<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testLoginPageDisplay(): void
    {
        $client = static::createClient();

        // Accéder à la page de connexion
        $crawler = $client->request('GET', '/login');

        // Vérifier que la réponse est réussie
        $this->assertResponseIsSuccessful();

        // Vérifier que le titre de la page contient "Se connecter"
        $this->assertSelectorTextContains('h1', 'Se connecter');

        // Vérifier que le formulaire contient les champs "email" et "password"
        $this->assertCount(1, $crawler->filter('input[name="name"]'));
        $this->assertCount(1, $crawler->filter('input[name="password"]'));

        // Vérifier qu'il y a un bouton de connexion
        $this->assertCount(1, $crawler->filter('button[type="submit"]'));
    }

    public function testLoginWithValidCredentials(): void
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

        // Vérifier que l'utilisateur est redirigé après une connexion réussie
        $this->assertResponseRedirects('/'); // Redirection après connexion (modifier si nécessaire)

        // Suivre la redirection et vérifier le contenu
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Produits phares');
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();

        // Accéder à la page d'identification
        $crawler = $client->request('GET', '/login');

        // Soumettre avec des identifiants invalides
        $form = $crawler->selectButton('Se Connecter')->form([
            'name' => 'invalid',       // Invalid username
            'password' => 'wrongpassword', // Invalid password
        ]);
        $client->submit($form);

        // Suivre la redirection
        $client->followRedirect();

        // Assert the response is successful
        $this->assertResponseIsSuccessful();

        // Assert an error message is displayed
        $this->assertSelectorTextContains('.alert-danger', 'Identifiants invalides.');
    }


}
