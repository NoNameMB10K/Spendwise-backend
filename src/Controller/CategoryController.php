<?php

namespace App\Controller;

use App\DTO\CategoryCreateDTO;
use App\DTO\CategoryDisplayDTO;
use App\DTO\CategorySumDTO;
use App\DTO\Mappers\CategoryMapper;
use App\Entity\Category;
use App\Repository\CartItemRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\QueryParameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categories')]
#[OA\Tag('Categories')]
class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private  CartItemRepository $cartItemRepository;
    private  ProductRepository $productRepository;
    /**
     * @param CategoryRepository $categoryRepository
     * @param CartItemRepository $cartItemRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(CategoryRepository $categoryRepository,  CartItemRepository $cartItemRepository, ProductRepository $productRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->productRepository = $productRepository;
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful response',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: CategorySumDTO::class)))
    )]
    #[QueryParameter(name: "date_from", description: "Start date (format: YYYY-MM-DD)")]
    #[QueryParameter(name: "date_to", description: "End date (format: YYYY-MM-DD)")]
    #[Route("/totalSpent", name: 'category_list_total_spent', methods: 'GET')]
    public function getTotalSpent(Request $request): JsonResponse
    {
        $dateFrom = $request->query->get('date_from');
        $dateTo = $request->query->get('date_to');

        return $this->json(CategoryMapper::mapToSumCategories(
            $this->categoryRepository->findAllCategoriesByDateRange($dateFrom, $dateTo),
            $this->cartItemRepository,
            $this->productRepository));
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful response',
        content: new Model(type: CategorySumDTO::class)
    )]
    #[Route('/totalSpent/{id}', name: 'category_show_total_spent', methods: 'GET')]
    #[QueryParameter(name: "date_from", description: "Start date (format: YYYY-MM-DD)")]
    #[QueryParameter(name: "date_to", description: "End date (format: YYYY-MM-DD)")]
    public function getIndividualTotalSpent(Category $category, Request $request): JsonResponse
    {
        $dateFrom = $request->query->get('date_from');
        $dateTo = $request->query->get('date_to');

        $categoryResult = $this->categoryRepository->findByCategoryIdAndDateRange($category->getId(), $dateFrom, $dateTo);
        if ($categoryResult !== null) {
            return $this->json(CategoryMapper::mapFromCategoryToSumDTO($categoryResult));
        } else {
            return $this->json(CategoryMapper::mapFromCategoryToSumDTO((array)$category));
        }
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful response',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: CategoryDisplayDTO::class)))
    )]
    #[Route(name: 'category_list', methods: 'GET')]
    public function index(Request $request): JsonResponse
    {
        $categoryDTOList = array_map(function ($category) {
            return CategoryMapper::mapFromCategoryToDTO($category);
        }, $this->categoryRepository->findAll());
        return $this->json($categoryDTOList);
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful response',
        content: new Model(type: CategoryDisplayDTO::class)
    )]
    #[Route('/{id}', name: 'category_show', methods: 'GET')]
    public function show(Category $category): JsonResponse
    {
        return $this->json(CategoryMapper::mapFromCategoryToDTO($category));
    }

    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: CategoryCreateDTO::class))
        )
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Created category',
        content: new Model(type: CategoryDisplayDTO::class)
    )]
    #[Route(name: 'category_create', methods: 'POST')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $category = new Category();
        $category->setName($data['name']);
        $this->categoryRepository->save($category);

        return $this->json(CategoryMapper::mapFromCategoryToDTO($category), Response::HTTP_CREATED);
    }

    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: CategoryCreateDTO::class))
        )
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Updated category',
        content: new Model(type: CategoryDisplayDTO::class)
    )]
    #[Route('/{id}', name: 'category_update', methods: 'PUT')]
    public function update(Category $category, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $category->setName($data['name']);
        $this->categoryRepository->save($category);

        return $this->json(CategoryMapper::mapFromCategoryToDTO($category), Response::HTTP_CREATED);
    }

    #[OA\Delete]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Deleted category',
        content: new OA\MediaType(
            mediaType: "application/json",
        )
    )]
    #[Route('/{id}', name: 'category_delete', methods: 'DELETE')]
    public function delete(Request $request, int $id): JsonResponse
    {
        $this->categoryRepository->delete($id);

        return new JsonResponse(['message' => Response::HTTP_OK]);
    }
}