<?php

namespace App\DTO\Mappers;

use App\DTO\CategoryDisplayDTO;
use App\DTO\CategorySumDTO;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use phpDocumentor\Reflection\Types\This;

class CategoryMapper
{
    public static function mapFromCategoryToDTO(Category $category): CategoryDisplayDTO
    {
        $dto = new CategoryDisplayDTO();
        $dto->setId($category->getId());
        $dto->setName($category->getName());

        return $dto;
    }

    public static function mapToSumCategories($categories, CartItemRepository $cartItemRepository, ProductRepository $productRepository)
    {
        $categorySumDTOs = array_map(function ($category) {
            return CategoryMapper::mapFromCategoryToSumDTO($category);
        }, $categories);


        $totalSum = $cartItemRepository->calculateSum();


        foreach ($categories as $category)
        {
            $products = array_map(function ($product) {
                return ProductMapper::mapFromProductToDTO($product);
            }, $productRepository->findByCategoryName($category["name"]));

            $sum = 0;
            foreach ($products as $product)
            {
                $sum = $sum + $cartItemRepository->calculateSumPerProduct($product->id);
            }

            foreach ($categorySumDTOs as $categorySumDTO)
            {
                if($categorySumDTO->id == $category["id"])
                {
                    $categorySumDTO->totalSpent = $sum;
                }
            }
        }


        return ['total_sum' => $totalSum, 'categories' => $categorySumDTOs];
    }

    public static function mapFromCategoryToSumDTO($category): CategorySumDTO
    {
        $dto = new CategorySumDTO();
        $dto->setId($category['id']);
        $dto->setName($category['name']);

        $dto->setTotalSpent($category['total_spent'] ?? 0);
        return $dto;
    }

}