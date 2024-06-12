<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class CategoryService
{
    private CategoryRepository $categoryRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function addNonExistingCategories(array $categoriesNames)
    {
        // Keep only the Categories that are not present in the database
        $categoriesToAddToDB = array_filter($categoriesNames, function ($categoryName) {
            $exists = $this->categoryRepository->findByName($categoryName);
            return $exists === null;
        });

        // Add non-existing Categories to the database
        foreach ($categoriesToAddToDB as $categoryName) {
            $newCategory = new Category();
            $newCategory->setName($categoryName);
            $this->categoryRepository->save($newCategory);
        }
    }
}