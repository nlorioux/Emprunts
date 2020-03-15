<?php

namespace App\Repository;

use App\Entity\Borrowing;
use App\Entity\Equipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @method Borrowing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Borrowing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Borrowing[]    findAll()
 * @method Borrowing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BorrowingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Borrowing::class);
    }

    public function findAllVisibleQuery(Equipment $equipment): Query
    {
        $qb = $this->createQueryBuilder('b')
            ->setParameter('equipment', $equipment)
            ->andWhere('b.equipment = :equipment')
            ->orderBy('b.startedOn', 'DESC')
            ->getQuery();
        return $qb;
    }

    public function countAllInProgress() {
        $qb = $this->createQueryBuilder('b')
            ->andWhere('b.inProgress = True')
            ->select('COUNT(b)')
            ->getQuery()
            ->getSingleScalarResult();
        return $qb;
    }

    public function countAllLate($today) {
        $qb = $this->createQueryBuilder('b')
            ->setParameter('today', $today)
            ->andWhere('b.inProgress = True')
            ->andWhere('b.endedOn < :today')
            ->select('COUNT(b)')
            ->getQuery()
            ->getSingleScalarResult();
        return $qb;
    }

    // /**
    //  * @return Borrowing[] Returns an array of Borrowing objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Borrowing
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
