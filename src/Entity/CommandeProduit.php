<?php

namespace App\Entity;

use App\Repository\CommandeProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeProduitRepository::class)]
class CommandeProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'Commandes')]
    private ?Utilisateur $CommandeProduit = null;

    /**
     * @var Collection<int, produit>
     */
    #[ORM\ManyToMany(targetEntity: produit::class)]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandeProduit(): ?Utilisateur
    {
        return $this->CommandeProduit;
    }

    public function setCommandeProduit(?Utilisateur $CommandeProduit): static
    {
        $this->CommandeProduit = $CommandeProduit;

        return $this;
    }

    /**
     * @return Collection<int, produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
        }

        return $this;
    }

    public function removeProduit(produit $produit): static
    {
        $this->produits->removeElement($produit);

        return $this;
    }
}
