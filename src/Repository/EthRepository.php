<?php

namespace App\Repository;

use App\Entity\Eth;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Eth>
 *
 * @method Eth|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eth|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eth[]    findAll()
 * @method Eth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eth::class);
    }

    public function getQbAll(): QueryBuilder
    {
        return $this->createQueryBuilder('e');
    }

    public function findLastSevenEth() {
        return $this->createQueryBuilder('e')
        ->orderBy('e.updateDate', 'desc')
        ->setMaxResults(7)
        ->getQuery()
        ->getResult();
    }

    public function findActualPrice() {
        return $this->createQueryBuilder('e')
        ->orderBy('e.updateDate', 'desc')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function findPreviousEthValue() {
        return $this->createQueryBuilder('e')
        ->orderBy('e.updateDate', 'desc')
        ->setMaxResults(2)
        ->getQuery()
        ->getResult()[1] ?? null;;
    }

//    /**
//     * @return Eth[] Returns an array of Eth objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Eth
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
