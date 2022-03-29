<?php

namespace App\Repository\Command;

use App\Entity\Command\TypeDeliverySelected;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeDeliverySelected|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeDeliverySelected|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeDeliverySelected[]    findAll()
 * @method TypeDeliverySelected[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeDeliverySelectedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDeliverySelected::class);
    }

    // /**
    //  * @return TypeDeliverySelected[] Returns an array of TypeDeliverySelected objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeDeliverySelected
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
