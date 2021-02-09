<?php

namespace App\Repository\User;

use App\Entity\User\TypeAccess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TypeAccess|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeAccess|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeAccess[]    findAll()
 * @method TypeAccess[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeAccessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeAccess::class);
    }

    // /**
    //  * @return TypeAccess[] Returns an array of TypeAccess objects
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
    public function findOneBySomeField($value): ?TypeAccess
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
