<?php

namespace App\DTO\Mappers;

use App\DTO\CartDTO;
use App\DTO\CartItemDTO;
use App\Entity\Cart;

class CartMapper
{
    public static function mapFromCartToCartDTO(Cart $cart): CartDTO
    {
        $cartDTO = new CartDTO();
        $cartDTO->setId($cart->getId());
        $cartDTO->setDateTime($cart->getDate());

        $cartItems = [];
        $sum = 0;
        foreach ($cart->cartItems as $item) {
            $cartItem = new CartItemDTO();
            $cartItem->setPrice($item->getPrice());
            $cartItem->setQuantity($item->getQuantity());
            $cartItem->setProductName($item->getProduct()->getName());

            $sum += $item->getPrice();
            $cartItems[] = $cartItem;
        }

        $cartDTO->setCartItems($cartItems);
        $cartDTO->setSum($sum);

        return $cartDTO;
    }
}