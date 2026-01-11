<?php

namespace App\Entity;

use App\Repository\ChauffeurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChauffeurRepository::class)]
class Chauffeur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 8, unique: true)]
    #[Assert\NotBlank(message: "Le CIN est obligatoire")]
    #[Assert\Length(
        exactly: 8,
        exactMessage: "Le CIN doit contenir exactement {{ limit }} chiffres"
    )]
    #[Assert\Regex(
        pattern: "/^[0-9]{8}$/",
        message: "Le CIN doit contenir 8 chiffres"
    )]
    private ?string $cin = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire")]
    #[Assert\Regex(
        pattern: "/^[0-9]{8}$/",
        message: "Le numéro de téléphone doit contenir 8 chiffres"
    )]
    private ?string $telephone = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide")]
    private ?string $email = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: "Le numéro de permis est obligatoire")]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: "Le numéro de permis doit contenir au moins {{ limit }} caractères"
    )]
    private ?string $numeroPermis = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le type de permis est obligatoire")]
    #[Assert\Choice(
        choices: ['B', 'C', 'D', 'E'],
        message: "Le type de permis doit être B, C, D ou E"
    )]
    private ?string $typePermis = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date de délivrance du permis est obligatoire")]
    #[Assert\LessThan(
        value: 'today',
        message: "La date de délivrance doit être antérieure à aujourd'hui"
    )]
    private ?\DateTimeInterface $dateDelivrancePermis = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date d'expiration du permis est obligatoire")]
    #[Assert\GreaterThan(
        value: 'today',
        message: "Le permis doit être valide (date d'expiration future)"
    )]
    private ?\DateTimeInterface $dateExpirationPermis = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date de naissance est obligatoire")]
    #[Assert\LessThan(
        value: '-21 years',
        message: "Le chauffeur doit avoir au moins 21 ans"
    )]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    private ?string $adresse = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "La ville est obligatoire")]
    private ?string $ville = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le statut est obligatoire")]
    #[Assert\Choice(
        choices: ['actif', 'inactif', 'en_conge', 'suspendu'],
        message: "Le statut doit être : actif, inactif, en_conge ou suspendu"
    )]
    private ?string $statut = 'actif';

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateModification = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    // Note: La relation avec Trajet sera ajoutée plus tard
    // quand l'entité Trajet sera créée

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): static
    {
        $this->cin = $cin;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getNumeroPermis(): ?string
    {
        return $this->numeroPermis;
    }

    public function setNumeroPermis(string $numeroPermis): static
    {
        $this->numeroPermis = $numeroPermis;
        return $this;
    }

    public function getTypePermis(): ?string
    {
        return $this->typePermis;
    }

    public function setTypePermis(string $typePermis): static
    {
        $this->typePermis = $typePermis;
        return $this;
    }

    public function getDateDelivrancePermis(): ?\DateTimeInterface
    {
        return $this->dateDelivrancePermis;
    }

    public function setDateDelivrancePermis(\DateTimeInterface $dateDelivrancePermis): static
    {
        $this->dateDelivrancePermis = $dateDelivrancePermis;
        return $this;
    }

    public function getDateExpirationPermis(): ?\DateTimeInterface
    {
        return $this->dateExpirationPermis;
    }

    public function setDateExpirationPermis(\DateTimeInterface $dateExpirationPermis): static
    {
        $this->dateExpirationPermis = $dateExpirationPermis;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;
        return $this;
    }

    // Méthodes helper pour la photo

    /**
     * Retourne le chemin complet de la photo
     */
    public function getPhotoPath(): ?string
    {
        return $this->photo ? '/uploads/chauffeurs/' . $this->photo : null;
    }

    /**
     * Retourne les initiales du chauffeur (pour affichage sans photo)
     */
    public function getInitiales(): string
    {
        $prenom = $this->prenom ?? '';
        $nom = $this->nom ?? '';
        
        $initialePrenom = !empty($prenom) ? strtoupper(substr($prenom, 0, 1)) : '';
        $initialeNom = !empty($nom) ? strtoupper(substr($nom, 0, 1)) : '';
        
        return $initialePrenom . $initialeNom;
    }

    /**
     * Vérifie si le chauffeur a une photo
     */
    public function hasPhoto(): bool
    {
        return $this->photo !== null && !empty($this->photo);
    }

    // Méthode utile pour afficher le nom complet
    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Méthode pour vérifier si le permis est valide
    public function isPermisValide(): bool
    {
        $today = new \DateTime();
        return $this->dateExpirationPermis > $today;
    }

    // Méthode pour calculer l'âge
    public function getAge(): int
    {
        $today = new \DateTime();
        return $today->diff($this->dateNaissance)->y;
    }

    // Méthode pour obtenir le badge de statut
    public function getStatutBadge(): string
    {
        return match($this->statut) {
            'actif' => 'success',
            'inactif' => 'secondary',
            'en_conge' => 'warning',
            'suspendu' => 'danger',
            default => 'secondary',
        };
    }

    public function __toString(): string
    {
        return $this->getNomComplet();
    }
}