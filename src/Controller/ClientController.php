<?php
namespace App\Controller;

use App\Repository\AbonnementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends AbstractController
{
    #[Route('/abonnements/front', name: 'app_abonnements_front')]
    public function index(AbonnementRepository $abonnementRepository): Response
    {
        $abonnements = $abonnementRepository->findAll();

        return $this->render('abonnement/frontabonnement.html.twig', [
            'abonnements' => $abonnements,
        ]);
    }

    #[Route('/recherche/abonne', name: 'recherche_front')]
    public function recherche(AbonnementRepository $abonnementRepository, Request $request): Response
    {
        $recherche = $request->query->get('recherche', '');
        $sport = $request->query->get('sport', '');
        $prixMin = $request->query->get('prix_min', null);
        $prixMax = $request->query->get('prix_max', null);

        $queryBuilder = $abonnementRepository->createQueryBuilder('a')
            ->leftJoin('a.typeAbonnement', 'ta');

        if ($recherche) {
            $queryBuilder->andWhere('ta.nom LIKE :recherche OR a.commentaires LIKE :recherche OR a.sport LIKE :recherche')
                         ->setParameter('recherche', "%$recherche%");
        }

        if ($sport) {
            $queryBuilder->andWhere('a.sport = :sport')
                         ->setParameter('sport', $sport);
        }

        if ($prixMin && is_numeric($prixMin)) {
            $queryBuilder->andWhere('a.prix >= :prix_min')
                         ->setParameter('prix_min', $prixMin);
        }

        if ($prixMax && is_numeric($prixMax)) {
            $queryBuilder->andWhere('a.prix <= :prix_max')
                         ->setParameter('prix_max', $prixMax);
        }

        $abonnements = $queryBuilder->getQuery()->getResult();

        return $this->render('abonnement/frontabonnement.html.twig', [
            'abonnements' => $abonnements,
        ]);
    }
}
