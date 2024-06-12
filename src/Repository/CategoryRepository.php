<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }


    public function save(Category $category)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($category);
        $entityManager->flush();
    }

    public function delete($id)
    {
        $entityManager = $this->getEntityManager();

        $category = $this->findById($id);
        if ($category !== null) {
            $entityManager->remove($category);
            $entityManager->flush();
        }
    }

    public function findById($categoryId): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $categoryId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByName($categoryName): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name = :val')
            ->setParameter('val', $categoryName)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function getCategoriesByDateRangeQuery($dateFrom = null, $dateTo = null): QueryBuilder
    {
        $query = $this->createQueryBuilder('c')
            ->join('c.products', 'p')
            ->join('p.cartItems', 'ci')
            ->join('ci.cart', 'ct');
        if ($dateFrom) {
            if ($dateTo !== null) {
                $query->where('ct.date >= :date_from')
                    ->setParameter('date_from', $dateFrom)
                    ->andWhere('ct.date <= :date_to')
                    ->setParameter('date_to', $dateTo);
            } else {
                $query->where('ct.date >= :date_from')
                    ->setParameter('date_from', $dateFrom);
            }
        }

        return $query
            ->select('c.id, c.name, SUM(ci.price) as total_spent')
            ->groupBy('c.id');
    }

    public function findAllCategoriesByDateRange($dateFrom = null, $dateTo = null): array
    {
        return $this->getCategoriesByDateRangeQuery($dateFrom, $dateTo)
            ->getQuery()
            ->getResult();
    }

    public function findByCategoryIdAndDateRange($categoryId, $dateFrom = null, $dateTo = null)
    {
        $query = $this->getCategoriesByDateRangeQuery($dateFrom, $dateTo)
            ->andWhere('c.id = :val')
            ->setParameter('val', $categoryId);

        return $query
            ->getQuery()
            ->getOneOrNullResult();
    }
}
