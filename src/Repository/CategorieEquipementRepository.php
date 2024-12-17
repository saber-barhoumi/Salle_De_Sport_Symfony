<?php

namespace App\Repository;

use App\Entity\CategorieEquipement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategorieEquipement>
 */
class CategorieEquipementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieEquipement::class);
    }
    public function findCategoryStatistics(): array
{
    return $this->createQueryBuilder('c')
        ->select('c.nom AS categoryName, COUNT(e.id) AS equipCount')
        ->leftJoin('c.equipements', 'e')
        ->groupBy('c.id')
        ->getQuery()
        ->getResult();
}


//    /**
//     * @return CategorieEquipement[] Returns an array of CategorieEquipement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CategorieEquipement
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}