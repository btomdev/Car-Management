<?php

namespace App\Repository;

use App\Domain\Car\DTO\CarFilterDTO;
use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    public const DQL_ALIAS = 'c';
    public const SORT_ASC = 'ASC';
    public const SORT_DESC = 'DESC';

    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Car::class);
    }

    public function paginate(int $page, CarFilterDTO $carDTO): PaginationInterface
    {
        $qb = $this->createQueryBuilder(self::DQL_ALIAS);
        $qb = $this->applyFilters($qb, $carDTO);

        return $this->paginator->paginate(
            $qb,
            $page,
            10,
            [
                'distinct' => false,
                'sortFieldAllowList' => ['c.brand', 'c.type', 'c.seats'],
            ]
        );
    }

    public function applyFilters(QueryBuilder $queryBuilder, CarFilterDTO $carDTO): QueryBuilder
    {
        if ($carDTO->brand) {
            $queryBuilder->andWhere(self::DQL_ALIAS.'.brand LIKE :brand')
                ->setParameter('brand', '%' . $carDTO->brand . '%');
        }

        if ($carDTO->type) {
            $queryBuilder->andWhere(self::DQL_ALIAS.'.type LIKE :type')
                ->setParameter('type', '%' . $carDTO->type . '%');
        }

        if ($carDTO->seats) {
            $queryBuilder->andWhere(self::DQL_ALIAS.'.seats LIKE :seats')
                ->setParameter('seats', '%' . $carDTO->seats . '%');
        }

        if ($carDTO->sort === self::SORT_ASC ) {
            $queryBuilder->orderBy(self::DQL_ALIAS . '.id', 'ASC');
        } elseif ($carDTO->sort === self::SORT_DESC) {
            $queryBuilder->orderBy(self::DQL_ALIAS . '.id', 'DESC');
        }


        return $queryBuilder;
    }
}
