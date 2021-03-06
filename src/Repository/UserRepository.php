<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return User[] Returns an array of User objects depending on role
     */
    public function findByRole($value)
    {

        $qb = $this->createQueryBuilder('user')
        ->where('user.roles = :role')
        ->setParameter('role', $value);


        $query = $qb->getQuery();

        return $query->execute();
    }

    public function searchInfluencerByUsername( $username){
        $builder = $this->createQueryBuilder('user');
        $builder->where(
            $builder->expr()->like('user.username', ":username")
        );
        $builder->setParameter('username', "%$username%");
        $builder->andHaving('user.roles = :role');
        $builder->setParameter('role', '["ROLE_INFLUENCER"]' );
        $query = $builder->getQuery();
        $result = $query->execute();
        return $result;
    }

    public function searchBrandByUsername( $username){
        $builder = $this->createQueryBuilder('user');
        $builder->where(
            $builder->expr()->like('user.username', ":username")
        );
        $builder->setParameter('username', "%$username%");
        $builder->andHaving('user.roles = :role');
        $builder->setParameter('role', '["ROLE_BRAND"]' );
        $query = $builder->getQuery();
        $result = $query->execute();
        return $result;
    }



    public function findOneByEmail($email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

  
/*     public function findUserFavoriteAnnouncements($id)
    {
        return $this->createQueryBuilder('user')
            ->leftJoin('user.favorites', 'announcement')
            ->addSelect('announcement')
            ->where('user.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    } */



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

}
