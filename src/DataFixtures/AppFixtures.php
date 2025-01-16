<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Product;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\File\File;

class AppFixtures extends Fixture
{   
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création de 10 utilisateurs
        for ($i = 0; $i<10; $i++){
            $user = new User();
            $user->setName('user'.$i);
            $user->setEmail('user'.$i.'@example.fr');
            $user->setAddress(mt_rand(0,100).' rue de Paris 78646 Versailles');
            $user->setRoles(['ROLE_USER']);

            $password = $this->hasher->hashPassword($user, 'pass_1234');
            $user->setPassword($password);
            $manager->persist($user);
        }

        // Création d'un utilisateur admin
        $user = new User();
        $user->setName('admin');
        $user->setEmail('admin@example.fr');
        $user->setAddress(mt_rand(0,100).' rue de Paris 78646 Versailles');
        $user->setRoles(['ROLE_ADMIN']);

        $password = $this->hasher->hashPassword($user, 'pass_1234');
        $user->setPassword($password);
        $manager->persist($user);

        // Création des produits
        $products = [
            ['Blackbelt','Sweet à capuche noir',29.90,'1.jpeg',1],
            ['BlueBelt','Sweet à capuche bleu',29.90,'2.jpeg',0],
            ['Street','Sweet à capuche orange',34.50,'3.jpeg',0],
            ['Pokeball','Sweet à capuche aux couleurs d\'une Pokéball',45,'4.jpeg',1],
            ['PinkLady','Sweet à capuche rose',29.90,'5.jpeg',0],
            ['Snow','Sweet à capuche blanc cassé',32,'6.jpeg',0],
            ['Greyback','Sweet à capuche gris',28.50,'7.jpeg',0],
            ['BlueCloud','Sweet à capuche bleu avec logo nuage bleu clair',45,'8.jpeg',0],
            ['BornInUsa','Sweet à capuche avec le drapeau des US',59.90,'9.jpeg',1],
            ['GreenSchool','Sweet à capuche vert avec logo jaune',42.20,'10.jpeg',0]
        ];

        foreach ($products as $key => $data) {
            $product = new Product();
            $product->setName($data[0]);
            $product->setDescription($data[1]);
            $product->setPrice($data[2]);
            $product->setImageName($data[3]);
            $product->setIsFeatured($data[4]);
            $product->setStockXs(mt_rand(10,50));
            $product->setStockS(mt_rand(10,50));
            $product->setStockM(mt_rand(10,50));
            $product->setStockL(mt_rand(10,50));
            $product->setStockXl(mt_rand(10,50));

            $manager->persist($product);
        }

        $manager->flush();
    }
}
