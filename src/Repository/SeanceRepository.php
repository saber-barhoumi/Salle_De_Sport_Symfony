<?php
namespace App\Repository;

use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class SeanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seance::class);
    }

    public function findBySort(?string $sortBy): array
    {
        $qb = $this->createQueryBuilder('s');
    
        if ($sortBy === 'date') {
            $qb->orderBy('s.date', 'DESC');
        }
    
        return $qb->getQuery()->getResult();
    }
    public function searchSeances(?string $category, ?string $objective): array
    {
        $qb = $this->createQueryBuilder('s');
    
        $qb->join('s.typeSeance', 'ts');
        if ($category) {
            $qb->andWhere('ts.type = :category')
               ->setParameter('category', $category);
        }
    
        if ($objective) {
            $qb->andWhere('s.objectif = :objective')
               ->setParameter('objective', $objective);
        }
    

        $qb->distinct();

        return $qb->getQuery()->getResult();
    }

}

