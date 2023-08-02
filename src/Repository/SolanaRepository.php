<?php

namespace App\Repository;

use App\Entity\Solana;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Solana>
 *
 * @method Solana|null find($id, $lockMode = null, $lockVersion = null)
 * @method Solana|null findOneBy(array $criteria, array $orderBy = null)
 * @method Solana[]    findAll()
 * @method Solana[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SolanaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Solana::class);
    }

//    /**
//     * @return Solana[] Returns an array of Solana objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Solana
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
