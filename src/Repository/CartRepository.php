<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 *
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }
    public function save(Cart $cart): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($cart);
        $entityManager->flush();
    }
    /**
     * @return array Returns an array of Cart objects
     */
    public function findByDateRange($dateFrom = null, $dateTo = null): array
    {
        $query = $this->createQueryBuilder('c');

        if ($dateFrom !== null) {
            if ($dateTo !== null) {
                $query->where('c.date >= :date_from')
                    ->setParameter('date_from', $dateFrom)
                    ->andWhere('c.date <= :date_to')
                    ->setParameter('date_to', $dateTo);
            } else {
                $query->where('c.date = :date_from')
                    ->setParameter('date_from', $dateFrom);
            }
        }
        return $query
            ->join('c.cartItems', 'ci')
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findCartWithItemsById($cartId): ?Cart
    {
        return $this->createQueryBuilder('c')
            ->join('c.cartItems', 'ci')
            ->andWhere('c.id = :val')
            ->setParameter('val', $cartId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
