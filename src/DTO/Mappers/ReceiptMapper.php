<?php

namespace App\DTO\Mappers;

use App\DTO\ReceiptDTO;
use App\DTO\ReceiptProductDTO;
use App\Repository\CategoryRepository;

class ReceiptMapper
{
    // For converting fields in Romanian to fields in English
    public static function mapFromResultsToReceiptDTO(array $results, CategoryRepository $categoryRepository): array
    {
        $receiptItems = [];
        foreach ($results as $categoryName => $products) {
            $category = $categoryRepository->findByName($categoryName);
            $item = new ReceiptDTO();
            $item->setId($category->id);
            $item->setName($category->name);

            $receiptProducts = [];
            foreach ($products as $product) {
                $receiptProduct = new ReceiptProductDTO();
                $receiptProduct->setName($product['nume produs']);
                $receiptProduct->setQuantity($product['cantitate']);
                $receiptProduct->setPrice((float)$product['pret']);

                $receiptProducts[] = $receiptProduct;
            }

            $item->setProducts($receiptProducts);
            $receiptItems[] = $item;
        }

        return $receiptItems;
    }

    // For mapping a ReceiptDTO from the Request Body's property
    public static function mapFromRequestToReceiptDTO(array $requestCategoriesProductsMap): array
    {
        $receiptItems = [];
        foreach ($requestCategoriesProductsMap as $categoryProductsMap) {
            $item = new ReceiptDTO();
            $item->setId($categoryProductsMap["id"]);
            $item->setName($categoryProductsMap["name"]);

            $receiptProducts = [];
            foreach ($categoryProductsMap["products"] as $product) {
                $receiptProduct = new ReceiptProductDTO();
                $receiptProduct->setName($product["name"]);
                $receiptProduct->setQuantity($product["quantity"]);
                $receiptProduct->setPrice((float)$product["price"]);

                $receiptProducts[] = $receiptProduct;
            }

            $item->setProducts($receiptProducts);
            $receiptItems[] = $item;
        }

        return $receiptItems;
    }
}