<?php

namespace App\Repository\User;

use App\Entity\User\Comment;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder as QueryBuilderOption;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findCommentsByUser(User $user) {
        $request = $this->createQueryBuilder('c')
            ->innerJoin('c.user', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId());
        return $request->getQuery()->getResult();
    }

    public function adminResearchComment(array $criteria) {
        $request = $this->createQueryBuilder('c')->where('c.delete = FALSE');

        $request = $this->optionsResearchComments($request, $criteria);

        if(array_key_exists('page', $criteria))
            $request->setFirstResult($criteria['page'] * $criteria['number_by_page']);

        if(array_key_exists('orderBy', $criteria))
            $request->orderBy('c.'.$criteria['orderBy']['attribut'], $criteria['orderBy']['order']);

        $request->setMaxResults($criteria['number_by_page']);

        return $request->getQuery()->getResult();
    }

    public function adminResearchNumberComments(array $criteria) {
        $request = $this->createQueryBuilder('c')->select('COUNT(c)')->where('c.delete = FALSE');

        $request = $this->optionsResearchComments($request, $criteria);

        return $request->getQuery()->getResult();
    }

    public function optionsResearchComments(QueryBuilderOption $request, array $criteria) {
        if(array_key_exists('text', $criteria))
            $request->andWhere("LOWER(c.text) LIKE LOWER(:text)")->setParameter('text', "%".$criteria['text']."%");

        if(array_key_exists('createdBefore', $criteria))
            $request->andWhere('DATE_DIFF(c.createdAt, :createdBefore) <= 0')
                ->setParameter('createdBefore', $criteria['createdBefore']);
        if(array_key_exists('createdAfter', $criteria))
            $request->andWhere('DATE_DIFF(c.createdAt, :createdAfter) >= 0')
                ->setParameter('createdAfter', $criteria['createdAfter']);

        if(array_key_exists('mark', $criteria)) {
            if($criteria['mark']['type'] == 'equal')
                $request->andWhere('c.mark = :mark')->setParameter('mark', $criteria['mark']['value']);
            else if($criteria['mark']['type'] == 'inferior')
                $request->andWhere('c.mark <= :mark')->setParameter('mark', $criteria['mark']['value']);
            else if($criteria['mark']['type'] == 'higher')
                $request->andWhere('c.mark >= :mark')->setParameter('mark', $criteria['mark']['value']);
        }

        return $request;
    }

    public function countNumberComments() {
        $request = $this->createQueryBuilder('c')->select('COUNT(c)')->where('c.delete = FALSE');
        return $request->getQuery()->getResult();
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
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
    public function findOneBySomeField($value): ?Comment
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
