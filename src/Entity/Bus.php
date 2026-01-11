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
    #[ORM\JoinColumn(nullable: true)]
    private ?Admin $admin = null;

    /**
     * @var Collection<int, Horaire>
     */
    #[ORM\OneToMany(targetEntity: Horaire::class, mappedBy: 'bus')]
    private Collection $horaires;

    public function __construct()
    {
        $this->horaires = new ArrayCollection();
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

    /**
     * @return Collection<int, Horaire>
     */
    public function getHoraires(): Collection
    {
        return $this->horaires;
    }

    public function addHoraire(Horaire $horaire): static
    {
        if (!$this->horaires->contains($horaire)) {
            $this->horaires->add($horaire);
            $horaire->setBus($this);
        }
        return $this;
    }

    public function removeHoraire(Horaire $horaire): static
    {
        if ($this->horaires->removeElement($horaire)) {
            if ($horaire->getBus() === $this) {
                $horaire->setBus(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->numero ?? '';
    }
}