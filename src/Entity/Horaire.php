<?php

namespace App\Entity;

use App\Repository\HoraireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HoraireRepository::class)]
class Horaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "La ville de départ est obligatoire")]
    private ?string $villeDepart = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "La ville d'arrivée est obligatoire")]
    private ?string $villeArrivee = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: "L'heure de départ est obligatoire")]
    private ?\DateTimeInterface $heureDepart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: "L'heure d'arrivée est obligatoire")]
    private ?\DateTimeInterface $heureArrivee = null;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotBlank(message: "Sélectionnez au moins un jour")]
    private array $joursActifs = [];

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\Positive(message: "Le prix doit être positif")]
    private ?string $prix = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $duree = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: "La distance doit être positive")]
    private ?int $distance = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(
        choices: ['actif', 'inactif'],
        message: "Le statut doit être actif ou inactif"
    )]
    private ?string $statut = 'actif';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModification = null;

    #[ORM\ManyToOne(inversedBy: 'horaires')]
    private ?Bus $bus = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->statut = 'actif';
    }

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVilleDepart(): ?string
    {
        return $this->villeDepart;
    }

    public function setVilleDepart(string $villeDepart): static
    {
        $this->villeDepart = $villeDepart;
        return $this;
    }

    public function getVilleArrivee(): ?string
    {
        return $this->villeArrivee;
    }

    public function setVilleArrivee(string $villeArrivee): static
    {
        $this->villeArrivee = $villeArrivee;
        return $this;
    }

    public function getHeureDepart(): ?\DateTimeInterface
    {
        return $this->heureDepart;
    }

    public function setHeureDepart(\DateTimeInterface $heureDepart): static
    {
        $this->heureDepart = $heureDepart;
        $this->calculerDuree();
        return $this;
    }

    public function getHeureArrivee(): ?\DateTimeInterface
    {
        return $this->heureArrivee;
    }

    public function setHeureArrivee(\DateTimeInterface $heureArrivee): static
    {
        $this->heureArrivee = $heureArrivee;
        $this->calculerDuree();
        return $this;
    }

    public function getJoursActifs(): array
    {
        return $this->joursActifs;
    }

    public function setJoursActifs(array $joursActifs): static
    {
        $this->joursActifs = $joursActifs;
        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): static
    {
        $this->duree = $duree;
        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): static
    {
        $this->distance = $distance;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeInterface $dateModification): static
    {
        $this->dateModification = $dateModification;
        return $this;
    }

    public function getBus(): ?Bus
    {
        return $this->bus;
    }

    public function setBus(?Bus $bus): static
    {
        $this->bus = $bus;
        return $this;
    }

    // Méthodes helper

    /**
     * Calcule automatiquement la durée du trajet
     */
    private function calculerDuree(): void
    {
        if ($this->heureDepart && $this->heureArrivee) {
            $diff = $this->heureDepart->diff($this->heureArrivee);
            
            $heures = $diff->h;
            $minutes = $diff->i;
            
            if ($heures > 0 && $minutes > 0) {
                $this->duree = $heures . 'h' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
            } elseif ($heures > 0) {
                $this->duree = $heures . 'h';
            } else {
                $this->duree = $minutes . 'min';
            }
        }
    }

    /**
     * Retourne le trajet formaté
     */
    public function getTrajet(): string
    {
        return $this->villeDepart . ' → ' . $this->villeArrivee;
    }

    /**
     * Retourne les jours actifs formatés
     */
    public function getJoursActifsFormates(): string
    {
        if (empty($this->joursActifs)) {
            return 'Aucun jour';
        }

        $joursTraduction = [
            'lundi' => 'Lun',
            'mardi' => 'Mar',
            'mercredi' => 'Mer',
            'jeudi' => 'Jeu',
            'vendredi' => 'Ven',
            'samedi' => 'Sam',
            'dimanche' => 'Dim'
        ];

        $joursAffiches = [];
        foreach ($this->joursActifs as $jour) {
            $joursAffiches[] = $joursTraduction[$jour] ?? $jour;
        }

        return implode(', ', $joursAffiches);
    }

    /**
     * Vérifie si l'horaire est actif un jour donné
     */
    public function isActifLe(string $jour): bool
    {
        return in_array(strtolower($jour), $this->joursActifs);
    }

    /**
     * Retourne le badge de statut
     */
    public function getStatutBadge(): string
    {
        return match($this->statut) {
            'actif' => 'success',
            'inactif' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Retourne l'heure de départ formatée
     */
    public function getHeureDepartFormatee(): string
    {
        return $this->heureDepart ? $this->heureDepart->format('H:i') : '';
    }

    /**
     * Retourne l'heure d'arrivée formatée
     */
    public function getHeureArriveeFormatee(): string
    {
        return $this->heureArrivee ? $this->heureArrivee->format('H:i') : '';
    }

    /**
     * Retourne le prix formaté
     */
    public function getPrixFormate(): string
    {
        return number_format((float)$this->prix, 2, ',', ' ') . ' DT';
    }

    public function __toString(): string
    {
        return $this->getTrajet() . ' - ' . $this->getHeureDepartFormatee();
    }
}