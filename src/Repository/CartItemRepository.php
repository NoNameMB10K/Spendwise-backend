<?php

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartItem>
 *
 * @method CartItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartItem[]    findAll()
 * @method CartItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }
    public function save(CartItem $cartItem): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($cartItem);
        $entityManager->flush();
    }
    public function getPriceByCart($cart): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.cart', 'cart')
            ->select('cart.id, cart.date, SUM(c.price) as sum')
            ->groupBy('cart.id')
            ->where('cart.id = :val')
            ->setParameter('val', $cart->getId())
            ->getQuery()
            ->getResult();
    }

    public function findByProductInCart($product ,$cart) {
        return $this->createQueryBuilder('ci')
            ->where('ci.cart = :cart')
            ->setParameter('cart', $cart)
            ->andWhere('ci.product = :product')
            ->setParameter('product', $product)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function calculateSum()
    {
        return $this->createQueryBuilder('c')
            ->select('SUM(c.quantity * c.price) as total')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function calculateSumPerProduct($productId)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.product', 'p')
            ->where('p.id = :productId')
            ->setParameter('productId', $productId)
            ->select('SUM(c.quantity * c.price) as total')
            ->getQuery()
            ->getSingleScalarResult();
    }

}
