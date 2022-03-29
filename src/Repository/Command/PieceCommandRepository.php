<?php

namespace App\Repository\Command;

use App\Entity\Command\PieceCommand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PieceCommand|null find($id, $lockMode = null, $lockVersion = null)
 * @method PieceCommand|null findOneBy(array $criteria, array $orderBy = null)
 * @method PieceCommand[]    findAll()
 * @method PieceCommand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PieceCommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PieceCommand::class);
    }

    // /**
    //  * @return PieceCommand[] Returns an array of PieceCommand objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PieceCommand
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
