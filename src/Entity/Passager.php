<?php

namespace App\Entity;

use App\Repository\PassagerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PassagerRepository::class)]
class Passager extends User
{
    public function __construct()
    {
        $this->setRoles(['ROLE_PASSAGER']);
    }
}