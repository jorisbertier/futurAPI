<?php

namespace App\Repository;

use App\Entity\BinanceCoin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BinanceCoin>
 *
 * @method BinanceCoin|null find($id, $lockMode = null, $lockVersion = null)
 * @method BinanceCoin|null findOneBy(array $criteria, array $orderBy = null)
 * @method BinanceCoin[]    findAll()
 * @method BinanceCoin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BinanceCoinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BinanceCoin::class);
    }

//    /**
//     * @return BinanceCoin[] Returns an array of BinanceCoin objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BinanceCoin
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
