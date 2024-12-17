<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le champ autorenouvellement est obligatoire.")]
    private ?bool $autorenouvellement = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Les commentaires ne peuvent pas être vides.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Les commentaires ne peuvent pas dépasser {{ limit }} caractères."
    )]
    private ?string $commentaires = null;

   #[ORM\ManyToOne(targetEntity: Typeabonnement::class)]
#[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
#[Assert\NotNull(message: "Le type d'abonnement est obligatoire.")]
private ?TypeAbonnement $typeAbonnement = null;


    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le sport est obligatoire.")]
    #[Assert\Length(
        max: 50,
        maxMessage: "Le sport ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $sport = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le prix est obligatoire.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    private ?float $prix = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La capacité est obligatoire.")]
    #[Assert\Positive(message: "La capacité doit être un nombre positif.")]
    #[Assert\LessThanOrEqual(
        value: 100,
        message: "La capacité ne peut pas dépasser 100 personnes."
    )]
    private ?int $capacite = null; 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAutorenouvellement(): ?bool
    {
        return $this->autorenouvellement;
    }

    public function setAutorenouvellement(bool $autorenouvellement): static
    {
        $this->autorenouvellement = $autorenouvellement;

        return $this;
    }

    public function getCommentaires(): ?string
    {
        return $this->commentaires;
    }

    public function setCommentaires(string $commentaires): static
    {
        $this->commentaires = $commentaires;

        return $this;
    }

    public function getTypeAbonnement(): ?TypeAbonnement
    {
        return $this->typeAbonnement;
    }

    public function setTypeAbonnement(?TypeAbonnement $typeAbonnement): self
    {
        $this->typeAbonnement = $typeAbonnement;

        return $this;
    }

    public function getSport(): ?string
    {
        return $this->sport;
    }

    public function setSport(string $sport): static
    {
        $this->sport = $sport;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

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
}
