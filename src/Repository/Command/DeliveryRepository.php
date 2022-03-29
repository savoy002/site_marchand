<?php

namespace App\Repository\Command;

use App\Entity\Command\Delivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder as QueryBuilderOption;

/**
 * @method Delivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Delivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Delivery[]    findAll()
 * @method Delivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Delivery::class);
    }

    public function companyResearchDeliveries(array $criteria)
    {
        $request = $this->createQueryBuilder('d');

        $request = $this->optionsResearchDeliveries($request, $criteria);

        if(array_key_exists('page', $criteria))
            $request->setFirstResult($criteria['page'] * $criteria['number_by_page']);

        if(array_key_exists('orderBy', $criteria))
            $request->orderBy('d.'.$criteria['orderBy']['attribut'], $criteria['orderBy']['order']);

        $request->setMaxResults($criteria['number_by_page']);

        return $request->getQuery()->getResult();
    }

    public function companyResearchNumberDeliveries(array $criteria) 
    {
        $request = $this->createQueryBuilder('d')->select('COUNT(d)');

        $request = $this->optionsResearchDeliveries($request, $criteria);

        return $request->getQuery()->getResult();
    }

    private function optionsResearchDeliveries(QueryBuilderOption $request, array $criteria) 
    {
        /*if(array_key_exists('company', $criteria))
            $request->innerJoin('d.type', 't');*/

        if(array_key_exists('sentBefore', $criteria))
            $request->andWhere('DATE_DIFF(d.date, :sentBefore) <= 0')->setParameter('sentBefore', $criteria['sentBefore']);
        if(array_key_exists('sentAfter', $criteria))
            $request->andWhere('DATE_DIFF(d.date, :sentAfter) >= 0')->setParameter('sentAfter', $criteria['sentAfter']);

        /*if(array_key_exists('type', $criteria))
            $request->andWhere('d.type = :id_type')->setParameter('id_type', $criteria['type']);*/

        if(array_key_exists('company', $criteria))
            $request->andWhere('d.company = :id_company')->setParameter('id_company', $criteria['company']);

        return $request;
    }

    /*public function findDeliveriesByCompany($id) 
    {
        $request = $this->createQueryBuilder('d')
            ->innerJoin('d.type', 't')
            ->where('t.company = :id')
            ->setParameter('id', $id);

        return $request->getQuery()->getResult();
    }*/

    /*public function findEmptyDeliveryByCompany($id)
    {
        $request = $this->createQueryBuilder('d')
            ->innerJoin('d.type', 't')
            ->where('d.empty = TRUE')
            ->andWhere('t.company = :id')
            ->setParameter('id', $id);

        return $request->getQuery()->getOneOrNullResult();
    }*/

    /*public function findOneDeliveryByCompany($id_company, $id_delivery)
    {
        $request = $this->createQueryBuilder('d')
            ->innerJoin('d.type', 't')
            ->where('d.id = :id_delivery')
            ->andWhere('t.company = :id_company')
            ->setParameter('id_delivery', $id_delivery)
            ->setParameter('id_company', $id_company);

        return $request->getQuery()->getOneOrNullResult();
    }*/


    // /**
    //  * @return Delivery[] Returns an array of Delivery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Delivery
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
