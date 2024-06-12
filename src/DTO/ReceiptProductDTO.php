<?php

namespace App\DTO;

class ReceiptProductDTO
{
    public string $name;
    public int $quantity;
    public float $price;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param mixed  $quantity
     */
    public function setQuantity(mixed $quantity): void
    {
        if (is_numeric($quantity) && (int)$quantity == $quantity) {
            $this->quantity = (int)$quantity;
        } else {
            $this->quantity = 0;
        }
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }


}