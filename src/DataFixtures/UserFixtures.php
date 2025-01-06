<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user-';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setName('User' . $i)
                ->setEmail('user' . $i . '@example.com')
                ->setPassword('password') // Remplacez par le hash d'un mot de passe
                ->setAdress('Address ' . $i);

            $manager->persist($user);

            // Ajoute une référence unique pour chaque utilisateur
            $this->addReference(self::USER_REFERENCE . $i, $user);
        }

        $manager->flush();
    }
}
