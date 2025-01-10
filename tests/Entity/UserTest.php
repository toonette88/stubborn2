<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSetAndGetName()
    {
        $user = new User();
        $user->setName('John Doe');

        $this->assertEquals('John Doe', $user->getName());
    }

    public function testSetAndGetEmail()
    {
        $user = new User();
        $user->setEmail('john.doe@example.com');

        $this->assertEquals('john.doe@example.com', $user->getEmail());
    }

    public function testSetAndGetPassword()
    {
        $user = new User();
        $user->setPassword('secret');

        $this->assertEquals('secret', $user->getPassword());
    }

    public function testSetAndGetAddress(): void
    {
        $user = new User();
    
        // Définir une adresse en tant que chaîne de caractères
        $user->setAddress('123 Main Street');
        $this->assertSame('123 Main Street', $user->getAddress());
    
        // Tester une adresse null
        $user->setAddress(null);
        $this->assertNull($user->getAddress());
    }

    public function testSetAndGetRoles()
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());  // Vérifier que le rôle ROLE_USER est toujours présent
    }

    public function testGetUserIdentifier()
    {
        $user = new User();
        $user->setName('John Doe');

        $this->assertEquals('John Doe', $user->getUserIdentifier());
    }

    public function testEraseCredentials(): void
    {
        // Créer un utilisateur avec un mot de passe temporaire
        $user = new User();
        $user->setPlainPassword('secret');
    
        // Vérifier que le mot de passe temporaire est bien "secret"
        $this->assertSame('secret', $user->getPlainPassword());
    
        // Appeler eraseCredentials() pour effacer le mot de passe
        $user->eraseCredentials();
    
        // Vérifier que le mot de passe est bien effacé (devrait être null)
        $this->assertNull($user->getPlainPassword());
    }
}
