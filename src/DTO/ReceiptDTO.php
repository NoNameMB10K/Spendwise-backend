<?php

namespace App\DTO;

class ReceiptDTO
{
    public int $id;
    public string $name;
    public array $products;

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

    /**
     * @return ReceiptProductDTO[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param ReceiptProductDTO[] $products
     */
    public function setProducts(array $products): void
    {
        $this->products = $products;
    }


}