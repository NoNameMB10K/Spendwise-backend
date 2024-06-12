<?php

namespace App\Controller;

use App\DTO\CartDTO;
use App\DTO\Mappers\CartMapper;
use App\Entity\Cart;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\QueryParameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/carts')]
#[OA\Tag('Carts')]
class CartController extends AbstractController
{
    private CartRepository $cartRepository;
    private CartItemRepository $cartItemRepository;

    /**
     * @param CartRepository $cartRepository
     * @param CartItemRepository $cartItemRepository
     */
    public function __construct(CartRepository $cartRepository, CartItemRepository $cartItemRepository)
    {
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
    }

    #[OA\Response(
        response: 200,
        description: 'Fetched all carts',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: CartDTO::class)))
    )]
    #[QueryParameter(name: "date_from", description: "Start date (format: YYYY-MM-DD)")]
    #[QueryParameter(name: "date_to", description: "End date (format: YYYY-MM-DD)")]
    #[Route(name: 'cart_list', methods: 'GET')]
    public function index(Request $request): JsonResponse
    {
        $dateFrom = $request->query->get('date_from');
        $dateTo = $request->query->get('date_to');

        $cartList = array_map(function ($cart) {
            return CartMapper::mapFromCartToCartDTO($cart);
        }, $this->cartRepository->findByDateRange($dateFrom, $dateTo));

        return $this->json($cartList);
    }

    #[OA\Response(
        response: 200,
        description: 'Fetched details of a cart',
        content: new Model(type: CartDTO::class)
    )]
    #[Route('/{id}', name: 'cart_show', methods: 'GET')]
    public function show(Cart $cart): JsonResponse
    {
        return $this->json(CartMapper::mapFromCartToCartDTO($this->cartRepository->findCartWithItemsById($cart->getId())));
    }
}