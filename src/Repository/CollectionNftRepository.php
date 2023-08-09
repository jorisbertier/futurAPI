<?php

namespace App\Repository;

use App\Entity\CollectionNft;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<CollectionNft>
 *
 * @method CollectionNft|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectionNft|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectionNft[]    findAll()
 * @method CollectionNft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectionNftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectionNft::class);
    }

    public function getQbAll(): QueryBuilder
    {
        return $this->createQueryBuilder('c');
    }
//    /**
//     * @return CollectionNft[] Returns an array of CollectionNft objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CollectionNft
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
