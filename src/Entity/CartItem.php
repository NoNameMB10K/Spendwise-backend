<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'cart_product')]
#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'cartItems')]
    #[ORM\JoinColumn(name: 'cart_id', nullable: false)]
    public ?Cart $cart = null;

    #[ORM\Column]
    public ?int $quantity = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'cartItems')]
    #[ORM\JoinColumn(name: 'product_id', nullable: false)]
    public ?Product $product = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    public ?float $price = null;

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): static
    {
        $this->cart = $cart;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

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
}
