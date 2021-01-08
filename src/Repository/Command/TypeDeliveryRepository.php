<?php

namespace App\Repository\Command;

use App\Entity\Command\TypeDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TypeDelivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeDelivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeDelivery[]    findAll()
 * @method TypeDelivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeDeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDelivery::class);
    }

    public function findFormSelectTypeDelivery($zip_code)
    {
        $request = $this->createQueryBuilder('t');
        if($zip_code === null || $zip_code == "") {
            $request->innerJoin('t.company', 'c')->where("t.delete = FALSE AND t.activate = TRUE AND c.area LIKE :all")
                ->setParameter('all', '%All%');
        } else {
            $request->innerJoin('t.company', 'c')
                ->where("t.delete = FALSE AND t.activate = TRUE AND (c.area LIKE :zip_code OR c.area LIKE :all)")
                ->setParameter('zip_code', substr($zip_code, 0, 2))
                ->setParameter('all', '%All%');
        }
        return $request;
    }

    public function findFormDelivery($id_company)
    {
        $request = $this->createQueryBuilder('t')->where('t.company = :id_company')->setParameter('id_company', $id_company);
        return $request;
    }

    // /**
    //  * @return TypeDelivery[] Returns an array of TypeDelivery objects
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
    public function findOneBySomeField($value): ?TypeDelivery
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
