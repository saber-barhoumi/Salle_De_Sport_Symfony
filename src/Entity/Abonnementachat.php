<?php
namespace App\Entity;
use App\Repository\AbonnementachatRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: AbonnementachatRepository::class)]
class Abonnementachat
{
     #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Abonnement::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Abonnement $abonnement = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateAchat = null;

  
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbonnement(): ?Abonnement
    {
        return $this->abonnement;
    }

    public function setAbonnement(?Abonnement $abonnement): self
    {
        $this->abonnement = $abonnement;
        return $this;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(\DateTimeInterface $dateAchat): self
    {
        $this->dateAchat = $dateAchat;
        return $this;
    }
}
