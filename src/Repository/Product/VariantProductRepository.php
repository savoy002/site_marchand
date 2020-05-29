<?php

namespace App\Repository\Product;

use App\Entity\Product\VariantProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method VariantProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method VariantProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method VariantProduct[]    findAll()
 * @method VariantProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VariantProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VariantProduct::class);
    }

    public function adminResearchVariantProduct(array $criteria)
    {
        $request = $this->createQueryBuilder('p')->where('p.delete = FALSE');

        if(array_key_exists('name', $criteria)) {
            if($criteria['name']['type'] == 'equal')
                $request->andWhere('p.name = :name')->setParameter('name', $criteria['name']['value']);
            else if($criteria['name']['type'] == 'contain')
                $request->andWhere("p.name LIKE :name")->setParameter('name', "%".$criteria['name']['value']."%");
        }
        if(array_key_exists('code', $criteria)) {
            if($criteria['code']['type'] == 'equal')
                $request->andWhere('p.code = :code')->setParameter('code', $criteria['code']['value']);
            else if($criteria['code']['type'] == 'contain')
                $request->andWhere("p.code LIKE :code")->setParameter('code', "%".$criteria['code']['value']."%");
        }
        if(array_key_exists('stock', $criteria)) {
            if($criteria['stock']['type'] == 'equal')
                $request->andWhere('p.stock = :stock')->setParameter('stock', $criteria['stock']['value']);
            else if($criteria['stock']['type'] == 'inferior')
                $request->andWhere("p.stock < :stock")->setParameter('stock', $criteria['stock']['value']);
            else if($criteria['stock']['type'] == 'higher')
                $request->andWhere("p.stock > :stock")->setParameter('stock', $criteria['stock']['value']);
        }
        if(array_key_exists('price', $criteria)) {
            if($criteria['price']['type'] == 'equal')
                $request->andWhere('p.price > :price AND p.price < :price + 100')->setParameter('price', $criteria['price']['value']);
            else if($criteria['price']['type'] == 'inferior')
                $request->andWhere('p.price > :price')->setParameter('price', $criteria['price']['value']);
            else if($criteria['price']['type'] == 'higher')
                $request->andWhere('p.price < :price')->setParameter('price', $criteria['price']['value']);
        }
        if(array_key_exists('description', $criteria))
            $request->andWhere("p.description LIKE :description")->setParameter('description', "%".$criteria['description']."%");
        if(array_key_exists('activate', $criteria)) {
            if($criteria['activate'] == 'activate')
                $request->andWhere("p.activate = :activate")->setParameter('activate', true);
            else if($criteria['activate'] == 'desactivate')
                $request->andWhere("p.activate = :activate")->setParameter('activate', false);
        }

        if(array_key_exists('page', $criteria))
            $request->setFirstResult($criteria['page'] * $criteria['number_by_page']);
        if(array_key_exists('orderBy', $criteria))
            $request->orderBy('p.'.$criteria['orderBy']['attribut'], $criteria['orderBy']['order']);

        $request->setMaxResults($criteria['number_by_page']);

        return $request->getQuery()->getResult();
    }

    public function adminResearchNumberVariantsProducts(array $criteria)
    {
        $request = $this->createQueryBuilder('p')->select('COUNT(p)')->where('p.delete = FALSE');

        if(array_key_exists('name', $criteria)) {
            if($criteria['name']['type'] == 'equal')
                $request->andWhere('p.name = :name')->setParameter('name', $criteria['name']['value']);
            else if($criteria['name']['type'] == 'contain')
                $request->andWhere("p.name LIKE :name")->setParameter('name', "%".$criteria['name']['value']."%");
        }
        if(array_key_exists('code', $criteria)) {
            if($criteria['code']['type'] == 'equal')
                $request->andWhere('p.code = :code')->setParameter('code', $criteria['code']['value']);
            else if($criteria['code']['type'] == 'contain')
                $request->andWhere("p.code LIKE :code")->setParameter('code', "%".$criteria['code']['value']."%");
        }
        if(array_key_exists('stock', $criteria)) {
            if($criteria['stock']['type'] == 'equal')
                $request->andWhere('p.stock = :stock')->setParameter('stock', $criteria['stock']['value']);
            else if($criteria['stock']['type'] == 'inferior')
                $request->andWhere("p.stock < :stock")->setParameter('stock', $criteria['stock']['value']);
            else if($criteria['stock']['type'] == 'higher')
                $request->andWhere("p.stock > :stock")->setParameter('stock', $criteria['stock']['value']);
        }
        if(array_key_exists('price', $criteria)) {
            if($criteria['price']['type'] == 'equal')
                $request->andWhere('p.price > :price AND p.price < :price + 100')->setParameter('price', $criteria['price']['value']);
            else if($criteria['price']['type'] == 'inferior')
                $request->andWhere('p.price > :price')->setParameter('price', $criteria['price']['value']);
            else if($criteria['price']['type'] == 'higher')
                $request->andWhere('p.price < :price')->setParameter('price', $criteria['price']['value']);
        }
        if(array_key_exists('description', $criteria))
            $request->andWhere("p.description LIKE :description")->setParameter('description', "%".$criteria['description']."%");
        if(array_key_exists('activate', $criteria)) {
            if($criteria['activate'] == 'activate')
                $request->andWhere("p.activate = :activate")->setParameter('activate', true);
            else if($criteria['activate'] == 'desactivate')
                $request->andWhere("p.activate = :activate")->setParameter('activate', false);
        }

        if(array_key_exists('orderBy', $criteria))
            $request->orderBy('p.'.$criteria['orderBy']['attribut'], $criteria['orderBy']['order']);



        return $request->getQuery()->getResult();
    }

    public function adminCountNumberVariantsProducts()
    {
        $request = $this->createQueryBuilder('p')->select('COUNT(p)')->where('p.delete = FALSE');
        return $request->getQuery()->getResult();
    }

    // /**
    //  * @return VariantProduct[] Returns an array of VariantProduct objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VariantProduct
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
