<?php

namespace App\Repository\Command;

use App\Entity\Command\CompanyDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyDelivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyDelivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyDelivery[]    findAll()
 * @method CompanyDelivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyDeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyDelivery::class);
    }

    // /**
    //  * @return CompanyDelivery[] Returns an array of CompanyDelivery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CompanyDelivery
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
