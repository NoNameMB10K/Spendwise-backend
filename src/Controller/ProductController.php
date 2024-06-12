<?php

namespace App\Controller;

use App\DTO\Mappers\ProductMapper;
use App\DTO\ProductCreateDTO;
use App\DTO\ProductDisplayDTO;
use App\Entity\Product;
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

#[Route('/products')]
#[OA\Tag('Products')]
class ProductController extends AbstractController
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;

    /**
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    #[OA\Response(
        response: 200,
        description: 'Fetched products',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: ProductDisplayDTO::class)))
    )]
    #[QueryParameter(name: "category", description: "Category name (e.g. Shoes)")]
    #[Route(name: 'product_list', methods: 'GET')]
    public function index(Request $request): JsonResponse
    {
        $category = $request->query->get('category');
        if ($category !== null) {
            $products = array_map(function ($product) {
                return ProductMapper::mapFromProductToDTO($product);
            }, $this->productRepository->findByCategoryName($category));

            return $this->json($products);
        }

        $products = array_map(function ($product) {
            return ProductMapper::mapFromProductToDTO($product);
        }, $this->productRepository->findAll());
        return $this->json($products);
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Fetched product based on id',
        content: new Model(type: ProductDisplayDTO::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Could not find product with that id'
    )]
    #[Route('/{id}', name: 'product_show', methods: 'GET')]
    public function show(Product $product): JsonResponse
    {
        return $this->json(ProductMapper::mapFromProductToDTO($product));
    }

    #[OA\Post(
        description: 'Add a name and an array of category ids',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: ProductCreateDTO::class))
        )
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Created product',
        content: new Model(type: ProductDisplayDTO::class)
    )]
    #[Route(name: 'product_create', methods: 'POST')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);

        foreach ($data['categories'] as $categoryId) {
            $category = $this->categoryRepository->findById($categoryId);
            $product->addCategory($category);
        }

        $this->productRepository->save($product);

        return $this->json(ProductMapper::mapFromProductToDTO($product), Response::HTTP_CREATED);
    }
}