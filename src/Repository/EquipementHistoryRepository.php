<?php
// src/Repository/EquipementHistoryRepository.php

namespace App\Repository;

use App\Entity\EquipementHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EquipementHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipementHistory::class);
    }

    // You can add custom queries here if necessary, for example:
    // public function findByEquipement($equipementId)
    // {
    //     return $this->createQueryBuilder('e')
    //         ->andWhere('e.equipement = :equipement')
    //         ->setParameter('equipement', $equipementId)
    //         ->orderBy('e.date', 'DESC')
    //         ->getQuery()
    //         ->getResult();
    // }
}
?>