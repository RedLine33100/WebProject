<?php

namespace App\Entity;

use App\Repository\ProduitCartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\UniqueConstraint(name: 'unique_triplet', columns: ["produit", "pays", "cart"])]
#[ORM\Entity(repositoryClass: ProduitCartRepository::class)]
class ProduitCart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'produit', nullable: false)]
    private ?Produit $produit = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'pays', nullable: false)]
    private ?Pays $pays = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'cart', nullable: false)]
    private ?Cart $cart = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

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

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): static
    {
        $this->cart = $cart;

        return $this;
    }
}
