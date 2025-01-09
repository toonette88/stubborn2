<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationTest extends WebTestCase
{
    public function testRegistration(): void
    {
        $client = static::createClient();

        //Accéder à la page d'inscription
        $crawler = $client->request('GET', '/register');

        //Vérifier que la page s'affiche correctement
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "S'inscrire");

        // Créer des données uniques pour chaque test
        $uniqueSuffix = time(); // Utiliser le timestamp pour un nom unique
        $username = 'Test User ' . $uniqueSuffix;
        
        //Soumettre le formulaire d'inscription
        $form = $crawler-> selectButton('Créer un compte')->form([
            'registration_form[name]' => $username,
            'registration_form[email]' => 'newUser@example.fr',
            'registration_form[adress]' => '4 Rue de Chanzy 21000 Dijon',
            'registration_form[plainPassword][first]' => '123456',
            'registration_form[plainPassword][second]' => '123456',
        ]);

        $client->submit($form);

        // Vérifier que l'utilisateur est redirigé après l'inscription
        $this->assertResponseRedirects('/');

        // Suivre la redirection
        $client->followRedirect();
    }
}
