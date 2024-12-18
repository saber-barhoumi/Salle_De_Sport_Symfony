<?php

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Seance::class, inversedBy: 'ratings')]
    #[ORM\JoinColumn(nullable: false)]
    private Seance $seance;

    #[ORM\Column(type: "integer")]
    #[Assert\Range(min: 1, max: 5, message: "La note doit être entre 1 et 5.")]
    private int $rating;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeance(): Seance
    {
        return $this->seance;
    }

    public function setSeance(Seance $seance): self
    {
        $this->seance = $seance;

        return $this;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
