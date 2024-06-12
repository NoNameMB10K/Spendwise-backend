<?php

namespace App\DTO;

class ProductCreateDTO
{
    public string $name;
    public array $categories;

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
     * @return integer[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param integer[] $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }


}