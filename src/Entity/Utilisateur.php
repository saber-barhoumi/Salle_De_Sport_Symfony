<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface; // Importez l'interface

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface // Implémentez PasswordAuthenticatedUserInterface ici
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)] // Modifiez la longueur pour un mot de passe haché plus long
    private ?string $mot_de_passe = null;

    #[ORM\Column]
    private ?int $age = null;

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
     
    /**
    * @ORM\Column(type="string", nullable=true)
    */
    private ?string $resetToken = null;


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

    public function getPassword(): string
    {
        return $this->mot_de_passe; // Retourne le mot de passe haché
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

    public function getRoles(): array
{
    // Assurez-vous que le TypeUtilisateur est défini
    if ($this->TypeUtilisateur === null) {
        return ['ROLE_USER']; // Rôle par défaut si aucun TypeUtilisateur n'est défini
    }

    // Générer le rôle en fonction du TypeUtilisateur
    $role = 'ROLE_' . strtoupper($this->TypeUtilisateur->getNom()); // Par exemple, ROLE_ADMIN, ROLE_EDITOR
    return [$role];
}


    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // Supprimez les données sensibles ici si nécessaire
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function __construct()
    {
    $this->statut = 'Inactif';
    $this->typeUtilisateur = null; // Ou assignez un TypeUtilisateur par défaut.
}
  

public function getResetToken(): ?string
{
    return $this->resetToken;
}

public function setResetToken(?string $resetToken): self
{
    $this->resetToken = $resetToken;
    return $this;
}

   
}
