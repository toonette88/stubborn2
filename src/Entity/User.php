<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 65)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $adress = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Cart::class)]
    private ?Cart $cart = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    // Méthode pour obtenir les rôles
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantie que l'utilisateur aura au moins le rôle ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    // Méthode pour définir les rôles
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    // Méthode pour obtenir l'identifiant de l'utilisateur
    public function getUserIdentifier(): string
    {
        return (string) $this->name;
    }

    // Méthode pour effacer les informations sensibles, comme le mot de passe en clair
    public function eraseCredentials(): void
    {
        // Par exemple, si vous avez une propriété "plainPassword" pour stocker un mot de passe non haché temporairement, vous pouvez la nettoyer ici
        // $this->plainPassword = null;
    }

    // Getter et setter pour les autres propriétés comme email, password, etc.
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    // **Implémentation de la méthode getPassword()**
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // **Implémentation de la méthode setPassword()**
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
}
