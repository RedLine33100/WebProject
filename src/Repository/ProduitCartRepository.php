<?php

namespace App\Repository;

use App\Entity\ProduitCart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProduitCart>
 *
 * @method ProduitCart|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitCart|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitCart[]    findAll()
 * @method ProduitCart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitCartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitCart::class);
    }

    //    /**
    //     * @return ProduitCart[] Returns an array of ProduitCart objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ProduitCart
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
