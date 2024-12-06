<?php

// src/Tests/Controller/RegistrationControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\Transport\NullTransport;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends WebTestCase
{


public function testRegister(): void
    {
        // Créer un transporteur null pour les tests
        $transport = new NullTransport();
        $mailer = new Mailer($transport);

        // Créer un client de test
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        // Vérifier que la page est chargée correctement
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="registration_form"]');

        // Créer des données uniques pour chaque test
        $uniqueSuffix = time(); // Utiliser le timestamp pour un nom unique
        $username = 'Test User ' . $uniqueSuffix;
        $email = 'testuser' . $uniqueSuffix . '@example.com';

        // Remplir le formulaire
        $form = $crawler->selectButton('Créer un compte')->form();
        $form['registration_form[name]'] = $username;
        $form['registration_form[email]'] = $email;
        $form['registration_form[plainPassword][first]'] = 'password123';
        $form['registration_form[plainPassword][second]'] = 'password123';
        $form['registration_form[adress]'] = '123 Test Street';

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifier que l'utilisateur est redirigé après l'inscription
        $this->assertResponseRedirects('/');


    }
}