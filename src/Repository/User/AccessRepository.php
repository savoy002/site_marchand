<?php

namespace App\Repository\User;

use App\Entity\User\Access;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Access|null find($id, $lockMode = null, $lockVersion = null)
 * @method Access|null findOneBy(array $criteria, array $orderBy = null)
 * @method Access[]    findAll()
 * @method Access[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Access::class);
    }

    // /**
    //  * @return Access[] Returns an array of Access objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Access
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
