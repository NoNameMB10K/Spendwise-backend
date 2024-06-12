<?php

namespace App\DTO;

class CategorySumDTO
{
    public int $id;
    public string $name;
    public float $totalSpent;



    /**
     * @return float
     */
    public function getTotalSpent(): float
    {
        return $this->totalSpent;
    }

    /**
     * @param float $totalSpent
     */
    public function setTotalSpent(float $totalSpent): void
    {
        $this->totalSpent = $totalSpent;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

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
}