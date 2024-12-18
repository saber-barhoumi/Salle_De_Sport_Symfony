<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        max: 50,
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "Le prix est obligatoire.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]

    private ?float $prix = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "La condition est obligatoire.")]
    private ?string $etat = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le fournisseur est obligatoire.")]
    #[Assert\Length(
        max: 50,
        maxMessage: "Le nom du fournisseur ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^\D+$/",
        message: "Le nom du fournisseur ne doit pas contenir de chiffres."
    )]
    private ?string $fournisseur = null;

    #[ORM\ManyToOne(inversedBy: 'equipements')]
    private ?CategorieEquipement $Equipement = null;

    #[ORM\Column(length: 255, nullable: true)] // Add nullable=true
    private ?string $photo = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $descriptions = null;

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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getFournisseur(): ?string
    {
        return $this->fournisseur;
    }

    public function setFournisseur(string $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getEquipement(): ?CategorieEquipement
    {
        return $this->Equipement;
    }

    public function setEquipement(?CategorieEquipement $Equipement): static
    {
        $this->Equipement = $Equipement;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDescriptions(): ?string
    {
        return $this->descriptions;
    }

    public function setDescriptions(string $descriptions): static
    {
        $this->descriptions = $descriptions;

        return $this;
    }
}
?>