<?php
// src/Repository/ProduitRepository.php
namespace App\Repository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\CategorieProduit;
use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findByCriteria($nom = null, $categorieProduit = null)
    {
        // Créer un QueryBuilder pour l'entité Produit
        $queryBuilder = $this->createQueryBuilder('p');

        // Ajouter des conditions à la requête en fonction des critères
        if ($nom) {
            $queryBuilder->andWhere('p.nom LIKE :nom')
                         ->setParameter('nom', '%' . $nom . '%');
        }

        if ($categorieProduit) {
            $queryBuilder->andWhere('p.CategorieProduit = :categorieProduit')
                         ->setParameter('categorieProduit', $categorieProduit);
        }

        // Exécuter la requête et retourner les résultats
        return $queryBuilder->getQuery()->getResult();
    }



    public function findByPriceRange(float $prixMin, float $prixMax)
    {
        // Crée un QueryBuilder pour l'entité "Produit"
        $qb = $this->createQueryBuilder('p')
                   ->where('p.prix BETWEEN :prixMin AND :prixMax')
                   ->setParameter('prixMin', $prixMin)
                   ->setParameter('prixMax', $prixMax);
    
        // Debug : Afficher la requête générée pour le débogage
        dump($qb->getQuery()->getDQL()); // Affiche la requête DQL générée
        dump($qb->getQuery()->getSQL()); // Facultatif : Affiche la requête SQL générée
    
        // Retourne les résultats
        return $qb->getQuery()->getResult();
    }






    //Statistique
    public function countProduits(): int
    {
        return $this->count([]);
    }
    public function getAveragePrice(): ?float
    {
    return $this->createQueryBuilder('p')
                ->select('AVG(p.prix)')
                ->getQuery()
                ->getSingleScalarResult();
    }
    public function getMaxPriceProduit(): ?array
{
    return $this->createQueryBuilder('p')
                ->select('p.nom', 'p.prix')
                ->orderBy('p.prix', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
}
public function getMinPriceProduit(): ?array
{
    return $this->createQueryBuilder('p')
                ->select('p.nom', 'p.prix')
                ->orderBy('p.prix', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
}

    

}
