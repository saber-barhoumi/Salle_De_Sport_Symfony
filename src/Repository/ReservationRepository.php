<?php
namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    // Méthode pour récupérer les équipements les plus réservés
    public function getMostReservedEquipments(): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->select('e.nom AS equipement, COUNT(r.id) AS reservation_count')
            ->join('r.equipement', 'e') // Joindre les équipements
            ->groupBy('e.id') // Grouper par équipement
            ->orderBy('reservation_count', 'DESC') // Trier par nombre de réservations
            ->setMaxResults(10); // Limiter à 10 équipements les plus réservés

        return $queryBuilder->getQuery()->getResult();
    }
}
