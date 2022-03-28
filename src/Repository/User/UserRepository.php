<?php

namespace App\Repository\User;

use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
//use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository //implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    ///**
    // * Used to upgrade (rehash) the user's password automatically over time.
    // */
    /*public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }*/

    public function adminResearchUser(array $criteria) {
        $request = $this->createQueryBuilder('u')->where('u.delete = FALSE');
        if(array_key_exists('username', $criteria)) {
            if($criteria['username']['type'] == 'equal')
                $request->andWhere('u.username = :username')->setParameter('username', $criteria['username']['value']);
            else if($criteria['username']['type'] == 'contain')
                $request->andWhere("u.username LIKE :username")->setParameter('username', "%".$criteria['username']['value']."%");
        }
        if(array_key_exists('email', $criteria)){
            if($criteria['email']['type'] == 'equal')
                $request->andWhere('u.email = :email')->setParameter('email', $criteria['email']['value']);
            else if($criteria['email']['type'] == 'contain')
                $request->andWhere("u.email LIKE :email")->setParameter('email', "%".$criteria['email']['value']."%");
        }
        if(array_key_exists('roles', $criteria)) 
            $request->andWhere('u.roles = :roles')->setParameter('roles', ($criteria['roles'] === 'user')?('["ROLE_USER"]'):('["ROLE_ADMIN"]'));
        if(array_key_exists('valid', $criteria))
            $request->andWhere('u.valid = :valid')->setParameter('valid', ($criteria['valid'] === 'verified')?(true):(false));
        if(array_key_exists('createdBy', $criteria))
            $request->andWhere('u.admin = :createdBy')->setParameter('createdBy', ($criteria['createdBy'] === 'admin')?(true):(false));
        if(array_key_exists('createdBefore', $criteria))
            $request->andWhere('DATE_DIFF(u.createdAt, :createdBefore) <= 0')->setParameter('createdBefore', $criteria['createdBefore']);
        if(array_key_exists('createdAfter', $criteria))
            $request->andWhere('DATE_DIFF(u.createdAt, :createdAfter) >= 0')->setParameter('createdAfter', $criteria['createdAfter']);
        if(array_key_exists('page', $criteria))
            $request->setFirstResult($criteria['page'] * $criteria['number_by_page']);
        if(array_key_exists('orderBy', $criteria))
            $request->orderBy('u.'.$criteria['orderBy']['attribut'], $criteria['orderBy']['order']);

        $request->setMaxResults($criteria['number_by_page']);

        return $request->getQuery()->getResult();
    }

    public function adminResearchNumberUsers(array $criteria) {
        $request = $this->createQueryBuilder('u')->select('COUNT(u)')->where('u.delete = FALSE');
        if(array_key_exists('username', $criteria)) {
            if($criteria['username']['type'] == 'equal')
                $request->andWhere('u.username = :username')->setParameter('username', $criteria['username']['value']);
            else if($criteria['username']['type'] == 'contain')
                $request->andWhere("u.username LIKE :username")->setParameter('username', "%".$criteria['username']['value']."%");
        }
        if(array_key_exists('email', $criteria)){
            if($criteria['email']['type'] == 'equal')
                $request->andWhere('u.email = :email')->setParameter('email', $criteria['email']['value']);
            else if($criteria['email']['type'] == 'contain')
                $request->andWhere("u.email LIKE :email")->setParameter('email', "%".$criteria['email']['value']."%");
        }
        if(array_key_exists('roles', $criteria)) 
            $request->andWhere('u.roles = :roles')->setParameter('roles', ($criteria['roles'] === 'user')?('["ROLE_USER"]'):('["ROLE_ADMIN"]'));
        if(array_key_exists('valid', $criteria))
            $request->andWhere('u.valid = :valid')->setParameter('valid', ($criteria['valid'] === 'verified')?(true):(false));
        if(array_key_exists('createdBy', $criteria))
            $request->andWhere('u.admin = :createdBy')->setParameter('createdBy', ($criteria['createdBy'] === 'admin')?(true):(false));
        if(array_key_exists('createdBefore', $criteria))
            $request->andWhere('DATE_DIFF(u.createdAt, :createdBefore) >= 0')->setParameter('createdBefore', $criteria['createdBefore']);
        if(array_key_exists('createdAfter', $criteria))
            $request->andWhere('DATE_DIFF(u.createdAt, :createdAfter) <= 0')->setParameter('createdAfter', $criteria['createdAfter']);
        if(array_key_exists('orderBy', $criteria))
            $request->orderBy('u.'.$criteria['orderBy']['attribut'], $criteria['orderBy']['order']);

        return $request->getQuery()->getResult();
    }

    public function countNumberUsers() {
        $request = $this->createQueryBuilder('u')->select('COUNT(u)')->where('u.delete = FALSE');
        return $request->getQuery()->getResult();
    }

    public function findFormCompanyDelivery() {
        $request = $this->createQueryBuilder('u')
            ->where("u.roles = '[\"ROLE_COMPANY_ADMIN\"]'")
            ->andWhere('u.delete = FALSE')
            ->andWhere('u.companyDelivery IS NULL');

        return $request;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
