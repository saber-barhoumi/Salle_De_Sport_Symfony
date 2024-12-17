<?php 
// src/Entity/EquipementHistory.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EquipementHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Equipement::class, inversedBy: 'histories')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Equipement $equipement = null;

    #[ORM\Column(type: "string")]
    private string $action;  // Action could be "added", "edited", "deleted"

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "string")]
    private string $user;  // Who performed the action

    public function __construct(Equipement $equipement, string $action, \DateTimeInterface $date, string $user)
    {
        $this->equipement = $equipement;
        $this->action = $action;
        $this->date = $date;
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipement(): ?Equipement
    {
        return $this->equipement;
    }

    public function setEquipement(Equipement $equipement): self
    {
        $this->equipement = $equipement;
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;
        return $this;
    }
}
?>


