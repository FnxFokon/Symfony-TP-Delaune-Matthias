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

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create('fr_Fr');
    }

    public function load(ObjectManager $manager): void
    {
        //
        $this->loadTypeBien($manager);
        $this->loadUser($manager);
        $this->loadCamping($manager);
        $this->loadBien($manager);


        $manager->flush();
    }

    // Méthode qui alimente les info du camping
    public function loadCamping(ObjectManager $manager)
    {
        // name, description, address, phone, zip_code, city, country, date_open
        $camping = new Camping();
        $camping->setName('Les Pins Tranquilles');
        $camping->setDescription('Bienvenue au camping "Les Pins Tranquilles", un havre de paix niché au cœur d\'une forêt luxuriante.');
        $camping->setAddress('123 Rue des Pins');
        $camping->setPhone('+12 345 6789');
        $camping->setZipcode('12345');
        $camping->setCity('Tranquilléville');
        $camping->setCountry('Imaginaria');
        $camping->setDateOpen(1714888800);
        $camping->setDateClose(1728511200);
        $manager->persist($camping);
    }

    // Méthode qui alimente les Types de Bien
    public function loadTypeBien(ObjectManager $manager)
    {
        // label, price, max_people
        $TypebienArray = [
            ['key' => 1, 'label' => 'Mobile-Home', 'price' => '50', 'maxpeople' => '3'],
            ['key' => 2, 'label' => 'Mobile-Home', 'price' => '54', 'maxpeople' => '4'],
            ['key' => 3, 'label' => 'Mobile-Home', 'price' => '57', 'maxpeople' => '5'],
            ['key' => 4, 'label' => 'Mobile-Home', 'price' => '64', 'maxpeople' => '8'],
            ['key' => 5, 'label' => 'Caravane', 'price' => '45', 'maxpeople' => '2'],
            ['key' => 6, 'label' => 'Caravane', 'price' => '48', 'maxpeople' => '4'],
            ['key' => 7, 'label' => 'Caravane', 'price' => '54', 'maxpeople' => '6'],
            ['key' => 8, 'label' => 'Emplacement tente', 'price' => '12', 'maxpeople' => '8'],
            ['key' => 9, 'label' => 'Emplacement tente', 'price' => '14', 'maxpeople' => '12'],
        ];

        foreach ($TypebienArray as $value) {

            $Typebien = new Typebien();
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
        $user = new User();
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

        for ($i = 2; $i <= 24; $i++) {
            $user = new User();
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
            $user = new User();
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

            $bien = new Bien();
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

            $bien = new Bien();
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

            $bien = new Bien();
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

            $bien = new Bien();
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

            $bien = new Bien();
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

            $bien = new Bien();
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
