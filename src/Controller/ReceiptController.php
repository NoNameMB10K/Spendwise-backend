<?php

namespace App\Controller;

use App\DTO\Mappers\ReceiptMapper;
use App\DTO\ReceiptDTO;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\CategoryService;
use App\Service\ChatGptImageService;
use App\Service\ReceiptService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/receipt')]
#[OA\Tag('receipt')]
class ReceiptController extends AbstractController
{
    private ReceiptService $receiptService;
    private ChatGptImageService $chatGptImageService;
    private CategoryRepository $categoryRepository;
    private CategoryService $categoryService;
    private  ProductRepository $productRepository;
    private CartRepository $cartRepository;
    private CartItemRepository $cartItemRepository;



    /**
     * @param ReceiptService $receiptService
     * @param ChatGptImageService $chatGptImageService
     * @param CategoryRepository $categoryRepository
     * @param CategoryService $categoryService
     * @param ProductRepository $productRepository
     * @param CartRepository $cartRepository
     * @param CartItemRepository $cartItemRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ReceiptService $receiptService, ChatGptImageService $chatGptImageService,
                                CategoryRepository $categoryRepository, CategoryService $categoryService, ProductRepository $productRepository,
                                CartRepository $cartRepository, CartItemRepository $cartItemRepository)
    {
        $this->receiptService = $receiptService;
        $this->chatGptImageService = $chatGptImageService;
        $this->categoryRepository = $categoryRepository;
        $this->categoryService = $categoryService;
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
    }

    #[OA\Post(
        description: 'Upload an image',
        requestBody: new OA\RequestBody(
            content: [new OA\MediaType(mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "image", type: "file", format: "binary"),
                        new OA\Property(property: "categories", type: 'string', default: [['name' => 'Carne'], ['name' => 'Suc'], ['name' => 'Tigari'], ['name' => 'Altele']]),
                    ]
                )
            )],
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Parsed receipt items',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: ReceiptDTO::class)))
    )]
    #[Route("/scan", name: 'receipt_scan', methods: 'POST')]
    public function scanReceipt(Request $request)
    {
        $imageFile = $request->files->get('image');

        if (is_null($imageFile)) {
            return $this->json(['error' => 'No image uploaded. Please upload an image.']);
        }

        $categories = json_decode($request->get('categories'), true);

        if (is_null($categories)) {
            return $this->json(['error' => 'Could not parse categories. Make sure there are no trailing commas, and that data is sent as array']);
        }

        // Send Image for Processing
        $imageOcrContent = $this->receiptService->processReceiptImage($imageFile);
        $categoryNames = array_map(function ($category) {
            return $category['name'];
        }, $categories);

        // Send OCR Response and Categories for GPT Processing
        $chatGptResponse = $this->chatGptImageService->processReceiptContents($categoryNames, $imageOcrContent);

        // Decode GPT Response
        $categoriesProductsMap = json_decode(json_decode($chatGptResponse), true);

        // Use Service to add non-existing Categories
        $categoriesNames = array_keys($categoriesProductsMap);
        $this->categoryService->addNonExistingCategories($categoriesNames);

        return $this->json(ReceiptMapper::mapFromResultsToReceiptDTO($categoriesProductsMap, $this->categoryRepository));

    }

    #[OA\Post(
        description: 'Send the receipt json',
        requestBody: new OA\RequestBody(
            content:  [new OA\MediaType(mediaType: "application/json",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "categoryProducts", type: "array", items: new OA\Items(ref: new Model(type: ReceiptDTO::class))),
                        new OA\Property(property: "date", type: 'string', format: "date"),
                    ]
                )
            )],
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Success"
    )]
    #[Route("/save", name: 'receipt_save', methods: 'POST')]
    public function saveReceipt(Request $request)
    {
        // Decode Request Body
        $jsonData = $request->getContent();
        $requestBody = json_decode($jsonData, true);

        // Create a new Cart
        $cart = new Cart();
        try {
            $date = new DateTime($requestBody["date"]);
            $cart->setDate($date);
        } catch (Exception) {
            return new JsonResponse(['error' => 'Invalid date string provided.'], 400);
        }
        // Save Cart to database
        $this->cartRepository->save($cart);

        $receipt = ReceiptMapper::mapFromRequestToReceiptDTO($requestBody["categoryProducts"]);

        foreach ($receipt as $categoryProductsMap) {
            $category = $this->categoryRepository->findByName($categoryProductsMap->name);
            foreach ($categoryProductsMap->products as $categoryProduct) {
                // Get or Create Product
                $product = $this->productRepository->findByName($categoryProduct->name);
                if ($product === null) {
                    // Create new Product
                    $newProduct = new Product();
                    $newProduct->setName($categoryProduct->name);
                    // Add new Product to the given Category
                    $newProduct->addCategory($category);
                    // Save to Database only if newly created
                    $this->productRepository->save($newProduct);
                    $product = $newProduct;
                }
                // Get or Create CartItem
                $cartItem = $this->cartItemRepository->findByProductInCart($product, $cart);
                if ($cartItem === null) {
                    // Create new CartItem
                    $cartItem = new CartItem();
                    $cartItem->setProduct($product);
                    $cartItem->setQuantity((int)$categoryProduct->quantity);
                    $cartItem->setPrice((float)$categoryProduct->price);
                    // Link Product with CartItem
                    $product->addCartItem($cartItem);
                    // Link CartItem with Cart
                    $cart->addCartItem($cartItem);
                } else {
                    $cartItem->setQuantity($cartItem->getQuantity() + (int)$categoryProduct->quantity);
                }
                $this->cartItemRepository->save($cartItem);
            }
        }

        return new JsonResponse("Successfully saved the Receipt.");
    }

}