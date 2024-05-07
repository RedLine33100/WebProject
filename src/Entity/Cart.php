<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, ProduitCart>
     */
    #[ORM\OneToMany(targetEntity: ProduitCart::class, mappedBy: 'cart', orphanRemoval: true)]
    private Collection $items;

    #[ORM\Column(name: 'is_paid')]
    private bool $isPaid = false;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(name: 'id_account', nullable: false)]
    #[Assert\NotNull(message: "account ne peux pas être null")]
    private ?Account $account = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ProduitCart>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ProduitCart $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setCart($this);
        }

        return $this;
    }

    public function removeItem(ProduitCart $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getCart() === $this) {
                $item->setCart(null);
            }
        }

        return $this;
    }

    public function getIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setPaid(bool $paid): static
    {
        $this->isPaid = $paid;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): static
    {
        $this->account = $account;

        return $this;
    }

}
