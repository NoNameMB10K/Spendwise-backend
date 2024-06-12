<?php

namespace App\DTO\Mappers;

use App\DTO\ProductDisplayDTO;
use App\Entity\Product;

class ProductMapper
{
    public static function mapFromProductToDTO(Product $product): ProductDisplayDTO
    {
        $productToDisplay = new ProductDisplayDTO();
        $productToDisplay->setId($product->getId());
        $productToDisplay->setName($product->getName());
        $categoryNameList = [];
        foreach ($product->getCategories() as $category) {
            $categoryNameList[] = $category->getName();
        }

        $productToDisplay->setCategories($categoryNameList);

        return $productToDisplay;
    }
}