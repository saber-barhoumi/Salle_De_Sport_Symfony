<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Regex(pattern: "/^[a-zA-Z\s\-]+$/", message: "Le nom de l'utilisateur doit contenir uniquement des lettres, des espaces et des tirets.")]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Assert\Regex(pattern: "/^[a-zA-Z\s\-]+$/", message: "Le prénom de l'utilisateur doit contenir uniquement des lettres, des espaces et des tirets.")]
    private ?string $prenom = null;

    #[ORM\Column]
    #[Assert\Positive(message: "L'âge doit être un nombre positif.")]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    private ?string $mot_de_passe = null;

    #[ORM\Column(length: 50)]
    private ?string $genre = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?TypeUtilisateur $TypeUtilisateur = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ['ROLE_USER', 'ROLE_ADMIN'], message: "Le rôle doit être soit 'ROLE_USER' soit 'ROLE_ADMIN'.")]
    private ?string $role = 'ROLE_USER';

    /**
     * @ORM\OneToMany(targetEntity=Favoris::class, mappedBy="utilisateur", orphanRemoval=true)
     */
    private $favoris;

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

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): static
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

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

    public function getTypeUtilisateur(): ?TypeUtilisateur
    {
        return $this->TypeUtilisateur;
    }

    public function setTypeUtilisateur(?TypeUtilisateur $TypeUtilisateur): static
    {
        $this->TypeUtilisateur = $TypeUtilisateur;

        return $this;
    }

    // ... Autres getters et setters ...

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->mot_de_passe;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // Supprimez les données sensibles si nécessaire
    }

    public function getRole(): ?string
{
    return $this->role;
}

public function setRole(string $role): self
{
    $this->role = $role;

    return $this;
}

public function getRoles(): array
{
    // Retourne le rôle défini dans l'attribut "role"
    return [$this->role];
}


    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function __construct()
    {
        $this->statut = 'Inactif';
        $this->TypeUtilisateur = null; // Ou assignez un TypeUtilisateur par défaut.
    }

    public function __toString(): string
{
    return $this->getPrenom(); // Replace `getUsername` with the actual property or method you want to use
}


    public function __constructFavoris()
    {
        $this->favoris = new ArrayCollection();
    }

    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Favoris $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
            $favori->setUtilisateur($this);
        }

        return $this;
    }

    public function removeFavori(Favoris $favori): self
    {
        if ($this->favoris->removeElement($favori)) {
            if ($favori->getUtilisateur() === $this) {
                $favori->setUtilisateur(null);
            }
        }

        return $this;
    }

}
