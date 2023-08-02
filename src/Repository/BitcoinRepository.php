<?php

namespace App\Repository;

use App\Entity\Bitcoin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bitcoin>
 *
 * @method Bitcoin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bitcoin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bitcoin[]    findAll()
 * @method Bitcoin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BitcoinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bitcoin::class);
    }

//    /**
//     * @return Bitcoin[] Returns an array of Bitcoin objects
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

//    public function findOneBySomeField($value): ?Bitcoin
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
