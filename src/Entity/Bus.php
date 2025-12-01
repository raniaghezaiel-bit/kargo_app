<?php

namespace App\Entity;

use App\Repository\BusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BusRepository::class)]
class Bus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: "Le numéro du bus est obligatoire")]
    private ?string $numero = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La capacité est obligatoire")]
    #[Assert\Positive(message: "La capacité doit être positive")]
    private ?int $capacite = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le type du bus est obligatoire")]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'bus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Admin $admin = null;

    // ❌ COMMENTEZ CETTE PARTIE (relation avec Trajet)
    // #[ORM\OneToMany(targetEntity: Trajet::class, mappedBy: 'bus')]
    // private Collection $trajets;

    public function __construct()
    {
        // $this->trajets = new ArrayCollection(); // ❌ COMMENTEZ AUSSI
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;
        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): static
    {
        $this->admin = $admin;
        return $this;
    }

    // ❌ COMMENTEZ OU SUPPRIMEZ toutes les méthodes getTrajets, addTrajet, removeTrajet
    
    public function __toString(): string
    {
        return $this->numero ?? '';
    }
}