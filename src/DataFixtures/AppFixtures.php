<?php

namespace App\DataFixtures;

use App\Entity\Bien;
use Faker\Generator;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Camping;
use App\Entity\Reservation;
use App\Entity\Typebien;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{

    // Propriété pour encoder le mot de passe
    private $encoder;

    // Propriété pour le faker
    private Generator $faker;

    // On remplie notre construct
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create('fr_Fr');
    }

    public function load(ObjectManager $manager): void
    {
        // ATTENTION l'ordre des "load" est très important

        //on récupère la méthode loadTypeBien
        $this->loadTypeBien($manager);
        //on récupère la méthode loadUser
        $this->loadUser($manager);
        //on récupère la méthode loadCamping
        $this->loadCamping($manager);
        //on récupère la méthode loadBien
        $this->loadBien($manager);

        // On injecte les données
        $manager->flush();
    }

    // Méthode qui alimente les info du camping
    public function loadCamping(ObjectManager $manager)
    {
        // On instancie notre class
        $camping = new Camping();
        // On set toutes ses propriétés
        $camping->setName('L’Espadrille Volante');
        $camping->setDescription('Bienvenue au camping "L’Espadrille Volante", un havre de paix niché au cœur d\'une forêt luxuriante.');
        $camping->setAddress('123 Rue des Pins');
        $camping->setPhone('+12 345 6789');
        $camping->setZipcode('12345');
        $camping->setCity('Tranquilléville');
        $camping->setCountry('Imaginaria');
        $camping->setDateOpen(1714888800);
        $camping->setDateClose(1728597599);
        $manager->persist($camping);
    }

    // Méthode qui alimente les Types de Bien
    public function loadTypeBien(ObjectManager $manager)
    {
        // On créer un tableau avec toutes nos colonne de Typebien (label, price, max_people)
        $TypebienArray = [
            ['key' => 1, 'label' => 'Mobile-Home', 'price' => '50', 'maxpeople' => 3],
            ['key' => 2, 'label' => 'Mobile-Home', 'price' => '54', 'maxpeople' => 4],
            ['key' => 3, 'label' => 'Mobile-Home', 'price' => '57', 'maxpeople' => 5],
            ['key' => 4, 'label' => 'Mobile-Home', 'price' => '64', 'maxpeople' => 8],
            ['key' => 5, 'label' => 'Caravane', 'price' => '45', 'maxpeople' => 2],
            ['key' => 6, 'label' => 'Caravane', 'price' => '48', 'maxpeople' => 4],
            ['key' => 7, 'label' => 'Caravane', 'price' => '54', 'maxpeople' => 6],
            ['key' => 8, 'label' => 'Emplacement tente 8m2', 'price' => '12', 'maxpeople' => 0],
            ['key' => 9, 'label' => 'Emplacement tente 12m2', 'price' => '14', 'maxpeople' => 0],
        ];

        // On boucle sur notre tableau 
        foreach ($TypebienArray as $value) {
            // On instancie notre class
            $Typebien = new Typebien();
            // On set toutes ses propriétés
            $Typebien->setLabel($value['label']);
            $Typebien->setprice($value['price']);
            $Typebien->setMaxPeople($value['maxpeople']);

            $manager->persist($Typebien);
            $this->addReference('typeBien_' . $value['key'], $Typebien);
        }
    }

    // Méthode qui alimente les User
    public function loadUser(ObjectManager $manager)
    {
        // Création de notre ADMIN
        // On instancie notre class
        $user = new User();
        // On set toutes ses propriétés
        $user->setEmail('admin@admin.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setLastname($this->faker->lastName)
            ->setFirstname($this->faker->firstName)
            ->setPhone($this->faker->phoneNumber)
            ->setAddress($this->faker->streetAddress)
            ->setZipcode($this->faker->postcode)
            ->setCity($this->faker->city)
            ->setCountry('France');
        $user->setPassword($this->encoder->hashPassword($user, 'admin'));
        $manager->persist($user);
        $this->addReference('user_1', $user);

        // Création de nos propriétaires
        for ($i = 2; $i <= 24; $i++) {
            // On instancie notre class
            $user = new User();
            // On set toutes ses propriétés
            $user->setEmail($this->faker->email)
                ->setRoles(['ROLE_OWNER'])
                ->setLastname($this->faker->lastName)
                ->setFirstname($this->faker->firstName)
                ->setPhone($this->faker->phoneNumber)
                ->setAddress($this->faker->streetAddress)
                ->setZipcode($this->faker->postcode)
                ->setCity($this->faker->city)
                ->setCountry('France');
            $user->setPassword($this->encoder->hashPassword($user, 'owner'));
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }

        for ($i = 25; $i <= 40; $i++) {
            // On instancie notre class
            $user = new User();
            // On set toutes ses propriétés
            $user->setEmail($this->faker->email)
                ->setRoles(['ROLE_USER'])
                ->setLastname($this->faker->lastName)
                ->setFirstname($this->faker->firstName)
                ->setPhone($this->faker->phoneNumber)
                ->setAddress($this->faker->streetAddress)
                ->setZipcode($this->faker->postcode)
                ->setCity($this->faker->city)
                ->setCountry('France');
            $user->setPassword($this->encoder->hashPassword($user, 'user'));
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }
    }

    // Méthode qui alimente les biens
    public function loadBien(ObjectManager $manager)
    {
        for ($i = 1; $i <= 11; $i++) {
            // On instancie notre class
            $bien = new Bien();
            // On set toutes ses propriétés
            $bien->setSize($this->faker->numberBetween(8, 12));
            $bien->setImagePath("mh3.jpg");
            $bien->setDescription(implode(',', $this->faker->words(10)));
            $bien->setTypebien($this->getReference('typeBien_1'));
            if ($i <= 5) {
                $bien->setUser($this->getReference('user_1'));
            } else {
                $bien->setUser($this->getReference('user_' . $i - 4));
            }

            $manager->persist($bien);
            $this->addReference('bien_' . $i, $bien);
        }

        for ($i = 12; $i <= 26; $i++) {
            // On instancie notre class
            $bien = new Bien();
            // On set toutes ses propriétés
            $bien->setSize($this->faker->numberBetween(10, 15));
            $bien->setImagePath("mh4.jpg");
            $bien->setDescription(implode(',', $this->faker->words(10)));
            $bien->setTypebien($this->getReference('typeBien_2'));
            if ($i <= 16) {
                $bien->setUser($this->getReference('user_1'));
            } else {
                $bien->setUser($this->getReference('user_' . $i - 8));
            }

            $manager->persist($bien);
            $this->addReference('bien_' . $i, $bien);
        }


        for ($i = 27; $i <= 37; $i++) {
            // On instancie notre class
            $bien = new Bien();
            // On set toutes ses propriétés
            $bien->setSize($this->faker->numberBetween(14, 18));
            $bien->setImagePath('mh5.jpeg');
            $bien->setDescription(implode(',', $this->faker->words(10)));
            $bien->setTypebien($this->getReference('typeBien_3'));
            if ($i <= 31) {
                $bien->setUser($this->getReference('user_1'));
            } else {
                $bien->setUser($this->getReference('user_' . $i - 14));
            }

            $manager->persist($bien);
            $this->addReference('bien_' . $i, $bien);
        }

        for ($i = 38; $i <= 50; $i++) {
            // On instancie notre class
            $bien = new Bien();
            // On set toutes ses propriétés
            $bien->setSize($this->faker->numberBetween(18, 25));
            $bien->setImagePath('mh8.jpeg');
            $bien->setDescription(implode(',', $this->faker->words(10)));
            $bien->setTypebien($this->getReference('typeBien_4'));
            if ($i <= 42) {
                $bien->setUser($this->getReference('user_1'));
            } else {
                $random = $this->faker->numberBetween(2, 24);
                $bien->setUser($this->getReference('user_' . $random));
            }

            $manager->persist($bien);
            $this->addReference('bien_' . $i, $bien);
        }

        for ($i = 51; $i <= 60; $i++) {
            // On instancie notre class
            $bien = new Bien();
            // On set toutes ses propriétés
            $bien->setDescription(implode(',', $this->faker->words(10)));
            $bien->setUser($this->getReference('user_1'));

            if ($i <= 54) {
                $bien->setSize($this->faker->numberBetween(8, 13));
                $bien->setImagePath('caravane2.jpg');
                $bien->setTypebien($this->getReference('typeBien_5'));
            } else if ($i <= 57) {
                $bien->setSize($this->faker->numberBetween(11, 16));
                $bien->setImagePath('caravane4.jpg');
                $bien->setTypebien($this->getReference('typeBien_6'));
            } else {
                $bien->setSize($this->faker->numberBetween(14, 19));
                $bien->setImagePath('caravane6.webp');
                $bien->setTypebien($this->getReference('typeBien_7'));
            }

            $manager->persist($bien);
            $this->addReference('bien_' . $i, $bien);
        }

        for ($i = 61; $i <= 90; $i++) {
            // On instancie notre class
            $bien = new Bien();
            // On set toutes ses propriétés
            $bien->setDescription(implode(',', $this->faker->words(10)));
            $bien->setUser($this->getReference('user_1'));

            if ($i <= 75) {
                $bien->setSize(8);
                $bien->setImagePath('tente8.jpg');
                $bien->setTypebien($this->getReference('typeBien_8'));
            } else {
                $bien->setSize(12);
                $bien->setImagePath('tente12.jpeg');
                $bien->setTypebien($this->getReference('typeBien_9'));
            }

            $manager->persist($bien);
            $this->addReference('bien_' . $i, $bien);
        }
    }
}
