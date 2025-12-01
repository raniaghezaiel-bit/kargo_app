<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Passager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un Admin
        $admin = new Admin();
        $admin->setEmail('admin@kargo.tn');
        $admin->setNom('Admin');
        $admin->setPrenom('Principal');
        $admin->setTelephone('71234567');
        $admin->setAdresse('Tunis, Tunisie');
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admin123')
        );
        $manager->persist($admin);

        // Créer un Passager
        $passager = new Passager();
        $passager->setEmail('passager@test.tn');
        $passager->setNom('Dupont');
        $passager->setPrenom('Jean');
        $passager->setTelephone('21234567');
        $passager->setAdresse('Ariana, Tunisie');
        $passager->setPassword(
            $this->passwordHasher->hashPassword($passager, 'pass123')
        );
        $manager->persist($passager);

        $manager->flush();
    }
}