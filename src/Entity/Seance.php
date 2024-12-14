<?php
namespace App\Entity;

use App\Repository\SeanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
#[ORM\Entity(repositoryClass: SeanceRepository::class)]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 30)]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s\-]+$/",
        message: "Le nom de la séance contient des caractères non autorisés. Utilisez uniquement des lettres, des chiffres, des espaces et des tirets."
    )]
    private ?string $nom = null;

    #[Assert\Callback]
    public function validateNomSeance(ExecutionContextInterface $context): void
    {
        if (strlen($this->nom) < 3) {
            $context->buildViolation('Le nom de la séance doit comporter au moins 3 caractères.')
                ->atPath('nom')
                ->addViolation();
        }
    }

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $capaciteMax = null;

    #[ORM\Column(length: 255)]
    private ?string $salle = null;

    #[ORM\Column(length: 25)]
    private string $statut;

    const STATUT_PROGRAMME = 'programmée';
    const STATUT_ANNULEE = 'annulée';

    public function __construct()
    {
        $this->statut = self::STATUT_PROGRAMME;
    }

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\Choice(
        choices: ['perdre du poids', 'se muscler', 'se défouler', 'entrainement avec dance','renforcer les muscles profonds'],
        message: 'Choisissez un objectif valide.'
    )]
    private $objectif;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $participantsinscrits = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\TypeSeance", fetch: "EAGER")]
    #[ORM\JoinColumn(name: "type_seance_id", referencedColumnName: "id")]
    private ?TypeSeance $typeSeance;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nomCoach = null;

    public function getNomCoach(): ?string
    {
        return $this->nomCoach;
    }

    public function setNomCoach(string $nomCoach): self
    {
        $this->nomCoach = $nomCoach;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getObjectif(): ?string
    {
        return $this->objectif;
    }

    public function setObjectif(string $objectif): self
    {
        $this->objectif = $objectif;
        return $this;
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

    public function getCapaciteMax(): ?int
    {
        return $this->capaciteMax;
    }

    public function setCapaciteMax(int $capaciteMax): static
    {
        $this->capaciteMax = $capaciteMax;
        return $this;
    }

    public function getSalle(): ?string
    {
        return $this->salle;
    }

    public function setSalle(string $salle): static
    {
        $this->salle = $salle;
        return $this;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getParticipantsinscrits(): ?int
    {
        return $this->participantsinscrits;
    }

    public function setParticipantsinscrits(int $participantsinscrits): static
    {
        $this->participantsinscrits = $participantsinscrits;
        return $this;
    }

    #[Assert\Callback]
    public function validateCapaciteMax(ExecutionContextInterface $context): void
    {
        if ($this->participantsinscrits > $this->capaciteMax) {
            $context->buildViolation('Le nombre de participants inscrits ne peut pas excéder la capacité maximale de la séance.')
                ->atPath('participantsinscrits')
                ->addViolation();
        }
    }

    public function getTypeSeance(): ?TypeSeance
    {
        return $this->typeSeance;
    }

    public function setTypeSeance(?TypeSeance $typeSeance): static
    {
        $this->typeSeance = $typeSeance;
        return $this;
    }

    public function getTauxOccupation(): float
    {
        if ($this->capaciteMax > 0) {
            return ($this->participantsinscrits / $this->capaciteMax) * 100;
        }
        return 0;
    }
}

