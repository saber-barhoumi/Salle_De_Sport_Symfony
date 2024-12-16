<?php

namespace App\Controller;

use App\Repository\AbonnementachatRepository;
use App\Repository\AbonnementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiqueController extends AbstractController
{
    #[Route('/statistiques', name: 'statistique_abonn')]
    public function index(
        AbonnementachatRepository $abonnementAchatRepository, 
        AbonnementRepository $abonnementRepository
    ): Response
    {
        $nombreAbonnementsParType = $abonnementAchatRepository->createQueryBuilder('aa')
            ->select('tb.nom AS typeAbonnement, COUNT(aa.id) AS nombre')
            ->innerJoin('aa.abonnement', 'a')
            ->innerJoin('a.typeAbonnement', 'tb')
            ->groupBy('tb.nom')
            ->getQuery()
            ->getResult();

        dump($nombreAbonnementsParType);

        $montantParType = $abonnementAchatRepository->createQueryBuilder('aa')
            ->select('tb.nom AS typeAbonnement, SUM(a.prix) AS montant')
            ->innerJoin('aa.abonnement', 'a')
            ->innerJoin('a.typeAbonnement', 'tb')
            ->groupBy('tb.nom')
            ->getQuery()
            ->getResult();

        dump($montantParType);

        $capaciteParSport = $abonnementRepository->createQueryBuilder('a')
            ->select('a.sport, SUM(a.capacite) AS capacite')
            ->groupBy('a.sport')
            ->getQuery()
            ->getResult();

        return $this->render('abonnement/statistique.html.twig', [
            'nombreAbonnementsParType' => $nombreAbonnementsParType,
            'montantParType' => $montantParType,
            'capaciteParSport' => $capaciteParSport,
        ]);
    }
}
