<?php

namespace App\DTO;

class CartDTO
{
    public int $id;
    public \DateTimeInterface $dateTime;
    public array $cartItems;
    public float $sum;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDateTime(): \DateTimeInterface
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function setDateTime(\DateTimeInterface $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return CartItemDTO[]
     */
    public function getCartItems(): array
    {
        return $this->cartItems;
    }

    /**
     * @param CartItemDTO[] $cartItems
     */
    public function setCartItems(array $cartItems): void
    {
        $this->cartItems = $cartItems;
    }

    /**
     * @return float
     */
    public function getSum(): float
    {
        return $this->sum;
    }

    /**
     * @param float $sum
     */
    public function setSum(float $sum): void
    {
        $this->sum = $sum;
    }


}