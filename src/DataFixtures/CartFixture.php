<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\ProductRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CartFixture extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private ProductRepository $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $faker->seed(mt_rand());
        $products = $this->productRepository->findAll();

        for ($i = 0; $i < 20; $i++) {
            $cart = new Cart();
            $cart->setDate($faker->dateTimeThisMonth());
            $manager->persist($cart);

            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setProduct($products[$i]);
            $cartItem->setQuantity($i);
            $cartItem->setPrice(mt_rand(50, 160));

            $manager->persist($cartItem);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProductFixture::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['carts'];
    }
}