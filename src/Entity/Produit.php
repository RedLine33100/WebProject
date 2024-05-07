<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, unique: true)]
    #[Assert\NotNull(message: "Name ne peut être null")]
    #[Assert\NotBlank(message: "Name ne peut être vide")]
    #[Assert\Length(min: 6, max:30, minMessage: "Name doit avoir au minimum {{ limit }} caractere", maxMessage: "Name doit avoir au maximum {{ limit }} caractere")]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull(message: "Description ne peut être null")]
    #[Assert\NotBlank(message: "Description ne peut être vide")]
    #[Assert\Length(min: 6, max:100, minMessage: "Description doit avoir au minimum {{ limit }} caractere", maxMessage: "Description doit avoir au maximum {{ limit }} caractere")]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Range(minMessage: "Prix doit être positif", min: 0)]
    private float $price = 0.0;

    #[ORM\Column]
    #[Assert\Range(minMessage: "Number doit être positif", min: 0)]
    private int $number = 0;

    /**
     * @var Collection<int, Pays>
     */
    #[ORM\ManyToMany(targetEntity: Pays::class, inversedBy: 'produits')]
    private Collection $pays;

    public function __construct()
    {
        $this->pays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Pays>
     */
    public function getPays(): Collection
    {
        return $this->pays;
    }

    public function addPay(Pays $pay): static
    {
        if (!$this->pays->contains($pay)) {
            $this->pays->add($pay);
        }

        return $this;
    }

    public function removePay(Pays $pay): static
    {
        $this->pays->removeElement($pay);

        return $this;
    }

    public function getNumber() :int
    {
        return $this->number;
    }

    public function setNumber(int $number) :Produit{
        $this->number = $number;
        return $this;
    }
}
