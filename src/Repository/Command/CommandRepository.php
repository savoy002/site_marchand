<?php

namespace App\Repository\Command;

use App\Entity\Command\Command;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder as QueryBuilderOption;

/**
 * @method Command|null find($id, $lockMode = null, $lockVersion = null)
 * @method Command|null findOneBy(array $criteria, array $orderBy = null)
 * @method Command[]    findAll()
 * @method Command[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Command::class);
    }

    public function adminResearchCommands(array $criteria) {
        $request = $this->createQueryBuilder('c');

        $request = $this->optionsResearchCommands($request, $criteria);

        if(array_key_exists('page', $criteria))
            $request->setFirstResult($criteria['page'] * $criteria['number_by_page']);

        if(array_key_exists('orderBy', $criteria))
            $request->orderBy('c.'.$criteria['orderBy']['attribut'], $criteria['orderBy']['order']);

        //if(array_key_exists('number_by_page', $criteria))
        $request->setMaxResults($criteria['number_by_page']);

        return $request->getQuery()->getResult();
    }

    public function adminResearchNumberCommands(array $criteria) {
        $request = $this->createQueryBuilder('c')->select('COUNT(c)');        

        $request = $this->optionsResearchCommands($request, $criteria);

        return $request->getQuery()->getResult();
    }

    private function optionsResearchCommands(QueryBuilderOption $request, array $criteria) {
        $request->andWhere('c.delete = FALSE')->andWhere('c.isBasket = FALSE');

        if( ( array_key_exists('sentBefore', $criteria) || array_key_exists('sentAfter', $criteria) || 
            array_key_exists('sentBefore', $criteria) ) ) {
            if(array_key_exists('status', $criteria)) {
                if($criteria['status'] != 'notSend') 
                    $request->innerJoin('c.delivery', 'd');
            } else
                $request->innerJoin('c.delivery', 'd');
        }

        if(array_key_exists('address', $criteria))
            $request->innerJoin('c.placeDel', 'a');

        if(array_key_exists('createdBefore', $criteria))
            $request->andWhere('DATE_DIFF(c.createdAt, :createdBefore) <= 0')
                ->setParameter('createdBefore', $criteria['createdBefore']);
        if(array_key_exists('createdAfter', $criteria))
            $request->andWhere('DATE_DIFF(c.createdAt, :createdAfter) >= 0')
                ->setParameter('createdAfter', $criteria['createdAfter']);

        if(array_key_exists('sentBefore', $criteria))
            $request->andWhere('DATE_DIFF(d.date, :sentBefore) <= 0')->setParameter('sentBefore', $criteria['sentBefore']);
        if(array_key_exists('sentAfter', $criteria))
            $request->andWhere('DATE_DIFF(d.date, :sentAfter) >= 0')->setParameter('sentAfter', $criteria['sentAfter']);

        if(array_key_exists('receivedBefore', $criteria))
            $request->andWhere('DATE_DIFF(c.dateReceive, :receivedBefore) <= 0')
                ->setParameter('receivedBefore', $criteria['receivedBefore']);
        if(array_key_exists('receivedAfter', $criteria))
            $request->andWhere('DATE_DIFF(c.dateReceive, :receivedAfter) >= 0')
                ->setParameter('receivedAfter', $criteria['receivedAfter']);

        if(array_key_exists('price', $criteria)) {
            if($criteria['price']['type'] == 'equal')
                $request->andWhere('c.priceTotal >= :price AND c.priceTotal < :price + 100')
                    ->setParameter('price', $criteria['price']['value']);
            else if($criteria['price']['type'] == 'inferior')
                $request->andWhere('c.priceTotal <= :price')->setParameter('price', $criteria['price']['value']);
            else if($criteria['price']['type'] == 'higher')
                $request->andWhere('c.priceTotal >= :price')->setParameter('price', $criteria['price']['value']);
        }

        if(array_key_exists('address', $criteria)) {
            if($criteria['address']['type'] == 'completed')
                $request
                    ->andWhere("LOWER(a.street) LIKE LOWER(:address) OR LOWER(a.zipCode) LIKE LOWER(:address) OR 
                        LOWER(a.city) LIKE LOWER(:address)")
                    ->setParameter('address', "%".$criteria['address']['value']."%");
            if($criteria['address']['type'] == 'street')
                $request->andWhere("LOWER(a.street) LIKE LOWER(:address)")
                        ->setParameter('address', "%".$criteria['address']['value']."%");
            if($criteria['address']['type'] == 'zip_code')
                $request->andWhere("LOWER(a.zipCode) LIKE LOWER(:address)")
                        ->setParameter('address', "%".$criteria['address']['value']."%");
            if($criteria['address']['type'] == 'city')
                $request->andWhere("LOWER(a.city) LIKE LOWER(:address)")->setParameter('address', "%".$criteria['address']['value']."%");
        }

        if(array_key_exists('status', $criteria)) {
            if($criteria['status'] === 'completed')
                $request->andWhere('c.completed = TRUE');
            if($criteria['status'] === 'notCompleted')
                $request->andWhere('c.completed = FALSE');
            if($criteria['status'] === 'notReceived')
                $request->andWhere('c.dateReceive IS NULL');
            if($criteria['status'] === 'notSend')
                $request->andWhere('c.delivery IS NULL');
        }

        return $request;
    }

    public function countNumberCommands() {
        $request = $this->createQueryBuilder('c')->select('COUNT(c)');
        return $request->getQuery()->getResult();
    }


    public function storeFindDeliveryTypeForAddress(array $option) {
        $request = $this->createQueryBuilder('t')
            ->innerJoin('t.company', 'c')
            ->andWhere('t.delete = FALSE AND t.activate = TRUE');
        if($option['zip_code'] === null || $option['zip_code'] == "") {
            $request->andWhere("'All' MEMBER OF c.area");
        } else {
            $request->andWhere(":zip_code MEMBRE OF c.area OR 'All' MEMBER OF c.area")
                ->setParameter('zip_code', substr($zip_code, 0, 2));
        }
        return $request;
    }


    // /**
    //  * @return Command[] Returns an array of Command objects
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
    public function findOneBySomeField($value): ?Command
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
