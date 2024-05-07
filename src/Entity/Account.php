<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "account")]
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class Account implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull(message: "Username peut pas être null")]
    #[Assert\NotBlank(message: "Username peut pas être vide")]
    #[Assert\Length(min: 4, max: 20, minMessage: "Lucas est interdit comme nom, minimum {{ limit }}", maxMessage: "Trop grand, {{ limit }} max")]
    private ?string $username = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull(message: "Lastname peut pas être null")]
    #[Assert\NotBlank(message: "Lastname peut pas être vide")]
    #[Assert\Length(min: 4, max: 20, minMessage: "Lucas est interdit comme prenom, minimum {{ limit }}", maxMessage: "Trop grand, {{ limit }} max")]
    private ?string $lastname = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotNull(message: "Password peut pas être null")]
    #[Assert\NotBlank(message: "Password peut pas être vide")]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Address peut pas être null")]
    #[Assert\NotBlank(message: "Address peut pas être vide")]
    #[Assert\Length(min: 6, max: 100, minMessage: "Addresse trop petite, minimum {{ limit }}", maxMessage: "Addresse trop grande, {{ limit }} max")]
    private ?string $address = null;

    /**
     * @var Collection<int, Cart>
     */
    #[ORM\OneToMany(targetEntity: Cart::class, mappedBy: 'account', orphanRemoval: true)]
    private Collection $carts;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Pays ne peut être null")]
    private ?Pays $pays = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotNull(message: "Email peut pas être null")]
    #[Assert\NotBlank(message: "Email peut pas être vide")]
    #[Assert\Length(min: 6, max: 30, minMessage: "Email trop petite minimum {{ limit }}", maxMessage: "Email trop grande {{ limit }} max")]
    private ?string $email = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $birthDate = null;

    #[ORM\Column]
    #[Assert\Range(minMessage: "Minimum 0 en type de compte", maxMessage: 'Maximum 2 en type de compte', min: 0, max: 2 )]
    private int $accountType = 0;

    public function __construct()
    {
        $this->roles = ["ROLE_USER"];
        $this->carts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role)
    {
        $this->roles[] = $role;
    }

    public function removeRole(string $role){
        $newRoles = [];
        foreach ($this->roles as $addRole){
            if($role == $addRole)
                continue;
            $newRoles[]=$addRole;
        }
        $this->setRoles($newRoles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function removeCart(Cart $cart): static
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getAccount() === $this) {
                $cart->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): static
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->setAccount($this);
        }

        return $this;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param \DateTime|null $birthDate
     */
    public function setBirthDate(?\DateTime $birthDate): Account
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     */
    public function setLastname(?string $lastname): Account
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return int
     */
    public function getAccountType(): int
    {
        return $this->accountType;
    }

    /**
     * @param int $accountType
     */
    public function setAccountType(int $accountType): Account
    {
        $this->accountType = $accountType;
        return $this;
    }
}
