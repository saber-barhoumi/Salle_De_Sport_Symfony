<?php

namespace App\Repository;

use App\Entity\Favoris;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FavorisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favoris::class);
    }

    /**
     * Trouver les favoris d'un utilisateur spécifique
     *
     * @param int $userId
     * @return Favoris[]
     */
    public function findFavorisByUser(int $userId)
    {
    
        return $this->createQueryBuilder('f')
            ->andWhere('f.utilisateur = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function addFavoris(Produit $produit, User $user)
    {
        $favoris = new Favoris();
        $favoris->setProduit($produit);
        $favoris->setUser($user);

        $this->_em->persist($favoris);
        $this->_em->flush();
    }
    /**
     * Supprimer un favori par produit et utilisateur
     *
     * @param int $produitId
     * @param int $userId
     * @return void
     */
    public function removeFavoris(int $produitId, int $userId)
    {
        $favori = $this->createQueryBuilder('f')
            ->andWhere('f.produit = :produitId')
            ->andWhere('f.utilisateur = :userId')
            ->setParameter('produitId', $produitId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();

        if ($favori) {
            $entityManager = $this->getEntityManager();
            $entityManager->remove($favori);
            $entityManager->flush();
        }
    }
    
}
