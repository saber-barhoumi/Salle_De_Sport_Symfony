<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Form\SeanceformType;
use App\Repository\SeanceRepository;
use App\Repository\TypeSeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SearchSeanceType;
use DateTime;
use DateInterval;
use App\Entity\Rating;
use Symfony\Component\HttpFoundation\JsonResponse;

class SeanceController extends AbstractController
{
    #[Route('/seance', name: 'app_seance')]
    public function index(): Response
    {
        return $this->render('seance/index.html.twig', [
            'controller_name' => 'SeanceController',
        ]);
    }

    #[Route('/seance/list', name: 'list_seance')]
    public function listSeance(Request $request, SeanceRepository $seanceRepository): Response
    {
        $form = $this->createForm(SearchSeanceType::class);
        $form->handleRequest($request);
    
        $sortBy = $form->isSubmitted() && $form->isValid()
            ? $form->get('sortBy')->getData()
            : null;
    
        $seances = $seanceRepository->findBySort($sortBy);
    
        return $this->render('seance/list.html.twig', [
            'seances' => $seances,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/seance/new', name: 'add_seance')]
    public function addSeance(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seance = new Seance();
        $form = $this->createForm(SeanceformType::class, $seance);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification des conflits : même horaire et même salle
            $existingSeances = $entityManager->getRepository(Seance::class)
                ->findBy(['date' => $seance->getDate(), 'salle' => $seance->getSalle()]);
    
            foreach ($existingSeances as $existingSeance) {
                if ($existingSeance->getDate() == $seance->getDate() &&
                    $existingSeance->getSalle() === $seance->getSalle()) {
                    $this->addFlash(
                        'error',
                        'Une autre séance est déjà planifiée à cet horaire dans cette salle.'
                    );
                    return $this->redirectToRoute('add_seance');
                }
            }
    
            $conflictingCoachSeances = $entityManager->getRepository(Seance::class)
                ->findBy(['date' => $seance->getDate(), 'nomCoach' => $seance->getNomCoach()]);
    
            if ($conflictingCoachSeances) {
                $this->addFlash(
                    'error',
                    'Le coach est déjà assigné à une autre séance à cet horaire.'
                );
                return $this->redirectToRoute('add_seance');
            }
    
            if (!preg_match('/^[a-zA-Z\s\-]+$/', $seance->getNom())) {
                $this->addFlash(
                    'error',
                    'Le nom de la séance ne doit contenir que des lettres, espaces et tirets.'
                );
                return $this->redirectToRoute('add_seance');
            }
    
            if ($seance->getParticipantsInscrits() > $seance->getCapaciteMax()) {
                $this->addFlash(
                    'error',
                    'Le nombre de participants inscrits dépasse la capacité maximale.'
                );
                return $this->redirectToRoute('add_seance');
            }
    
            if ($seance->getParticipantsInscrits() < 0) {
                $this->addFlash(
                    'error',
                    'Le nombre de participants inscrits doit être un entier positif.'
                );
                return $this->redirectToRoute('add_seance');
            }
    
            $entityManager->persist($seance);
            $entityManager->flush();
    
            $this->addFlash('success', 'La séance a été ajoutée avec succès.');
            return $this->redirectToRoute('list_seance');
        }
    
        return $this->render('seance/nouveau.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/seance/delete/{id}', name: 'delete_seance')]
    public function deleteSeance(int $id, EntityManagerInterface $entityManager): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->find($id);

        if ($seance) {
            $entityManager->remove($seance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('list_seance');
    }

    #[Route('/seance/modify/{id}', name: 'modify_seance')]
    public function modifySeance(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->find($id);
        
        if (!$seance) {
            throw $this->createNotFoundException('Séance non trouvée');
        }
    
        $form = $this->createForm(SeanceformType::class, $seance);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $existingSeances = $entityManager->getRepository(Seance::class)
                ->findBy(['date' => $seance->getDate(), 'salle' => $seance->getSalle()]);
    
            foreach ($existingSeances as $existingSeance) {
                if ($existingSeance->getId() !== $seance->getId()) {
                    $this->addFlash('error', 'Une autre séance est déjà planifiée à cet horaire dans cette salle.');
                    return $this->redirectToRoute('modify_seance', ['id' => $id]);
                }
            }
            $conflictingCoachSeances = $entityManager->getRepository(Seance::class)
                ->findBy(['date' => $seance->getDate(), 'nomCoach' => $seance->getNomCoach()]);
    
            foreach ($conflictingCoachSeances as $conflictingCoachSeance) {
                if ($conflictingCoachSeance->getId() !== $seance->getId()) {
                    $this->addFlash('error', 'Le coach est déjà assigné à une autre séance à cet horaire.');
                    return $this->redirectToRoute('modify_seance', ['id' => $id]);
                }
            }
    
            if (!preg_match('/^[a-zA-Z\s\-]+$/', $seance->getNom())) {
                $this->addFlash('error', 'Le nom de la séance ne doit contenir que des lettres, espaces et tirets.');
                return $this->redirectToRoute('modify_seance', ['id' => $id]);
            }

            if ($seance->getParticipantsInscrits() > $seance->getCapaciteMax()) {
                $this->addFlash('error', 'Le nombre de participants inscrits dépasse la capacité maximale.');
                return $this->redirectToRoute('modify_seance', ['id' => $id]);
            }
    
            if ($seance->getParticipantsInscrits() < 0) {
                $this->addFlash('error', 'Le nombre de participants inscrits doit être un entier positif.');
                return $this->redirectToRoute('modify_seance', ['id' => $id]);
            }
    
            $typeSeance = $form->get('typeSeance')->getData();
            $seance->setTypeSeance($typeSeance);
    
            $objectif = $form->get('objectif')->getData();
            $seance->setObjectif($objectif);
    
            $entityManager->persist($seance);
            $entityManager->flush();
    
            $this->addFlash('success', 'La séance a été modifiée avec succès.');
            return $this->redirectToRoute('list_seance');
        }
    
        return $this->render('seance/modify.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    private SeanceRepository $seanceRepository;

    public function __construct(SeanceRepository $seanceRepository)
    {
        $this->seanceRepository = $seanceRepository;
    }


    #[Route('home/classes/search-classes', name:'search_classes', methods :["GET"])]
    public function search(Request $request): Response
    {
        $category = $request->query->get('category'); 
        $objective = $request->query->get('objective');
    
        $seances = $this->seanceRepository->searchSeances($category, $objective);

        $seancesUnique = [];
        foreach ($seances as $seance) {
            $seancesUnique[$seance->getNom()] = $seance;
        }
    
        $seancesUnique = array_values($seancesUnique);
        return $this->render('search_results.html.twig', [
            'seances' => $seancesUnique,
        ]);
    }
    #[Route('/seance/stats', name: 'stats_seances')]
    public function statsSeances(SeanceRepository $seanceRepository, TypeSeanceRepository $typeSeanceRepository): Response
    {
        $seancesAnnulees = $seanceRepository->createQueryBuilder('s')
        ->select('s.nom AS nomSeance, s.date AS dateSeance, s.statut AS statutSeance')
        ->where('s.statut = :statut')
        ->setParameter('statut', 'annulée')
        ->getQuery()
        ->getResult();

        $seancesParCategorie = $seanceRepository->createQueryBuilder('s')
            ->select('t.type AS typeSeance, COUNT(s.id) AS nombreSeances')
            ->leftJoin('s.typeSeance', 't')
            ->groupBy('t.id')
            ->getQuery()
            ->getResult();

        $tauxOccupationParCategorie = $typeSeanceRepository->createQueryBuilder('t')
        ->select('t.type AS typeSeance, 
                  SUM(s.participantsinscrits) AS totalParticipants, 
                  SUM(s.capaciteMax) AS totalCapacite,
                  (SUM(s.participantsinscrits) / SUM(s.capaciteMax)) * 100 AS tauxOccupation')
        ->leftJoin('t.seances', 's')
        ->groupBy('t.id')
        ->getQuery()
        ->getResult();

        $participantsParSeance = $seanceRepository->createQueryBuilder('s')
            ->select('s.nom AS nomSeance, 
                      SUM(s.participantsinscrits) AS totalParticipants,
                      AVG(s.participantsinscrits) AS moyenneParticipants')
            ->groupBy('s.nom')
            ->getQuery()
            ->getResult();

        $stats = [];
        foreach ($seancesParCategorie as $categorie) {
            $typeSeance = $categorie['typeSeance'];
            $nombreSeances = $categorie['nombreSeances'];
            $tauxOccupation = 0;

            foreach ($tauxOccupationParCategorie as $occupation) {
                if ($occupation['typeSeance'] == $typeSeance) {
                    $tauxOccupation = $occupation['tauxOccupation'];
                    break;
                }
            }
            $stats[] = [
                'typeSeance' => $typeSeance,
                'nombreSeances' => $nombreSeances,
                'tauxOccupation' => $tauxOccupation,
            ];
        }
        foreach ($participantsParSeance as $seance) {
            $nomSeance = $seance['nomSeance'];
            $totalParticipants = $seance['totalParticipants'];
            $moyenneParticipants = $seance['moyenneParticipants'];

            $stats[] = [
                'nomSeance' => $nomSeance,
                'totalParticipants' => $totalParticipants,
                'moyenneParticipants' => $moyenneParticipants,
            ];
        }
        return $this->render('stats.html.twig', [
            'participantsParSeance' => $participantsParSeance,
            'tauxOccupationParCategorie' => $tauxOccupationParCategorie,
            'stats' => $stats,
            'seancesAnnulees' => $seancesAnnulees,
        ]);
    }

    #[Route('/home/classes', name: 'app_hclasses', methods: ['GET'])]
    public function afficheSeances(SeanceRepository $repo): Response
    {
        $currentDate = new DateTime();
        $startOfWeek = clone $currentDate;
        $startOfWeek->setISODate($currentDate->format('Y'), $currentDate->format('W'));
        $endOfWeek = clone $startOfWeek;
        $endOfWeek->add(new DateInterval('P6D'));
        $seances = $repo->findAll();
        $timetable = [];
        $uniqueSeances = [];
    
        foreach ($seances as $seance) {
            $date = $seance->getDate();
            if ($date >= $startOfWeek && $date <= $endOfWeek) {
                $nom = $seance->getNom();
                $hour = $date->format('H');
                $day = $date->format('l'); 
                if (!isset($uniqueSeances[$nom])) {
                    $uniqueSeances[$nom] = [
                        'nom' => $nom,
                        'categorie' => $seance->getTypeSeance(),
                    ];
                }
                if (!isset($timetable[$day])) {
                    $timetable[$day] = [];
                }
                if (!isset($timetable[$day][$hour])) {
                    $timetable[$day][$hour] = [];
                }
                $timetable[$day][$hour][] = $seance;
            }
        }
        return $this->render('class-details.html.twig', [
            'seances' => $seances,
            'timetable' => $timetable,
            'start_of_week' => $startOfWeek,
            'end_of_week' => $endOfWeek,
            'uniqueSeances' => $uniqueSeances,
        ]);
    }
    #[Route('/home/classes/{id}', name: 'app_classes_seances')]
    public function seanceDetails(int $id, EntityManagerInterface $entityManager): Response
    {
    $seance = $entityManager->getRepository(Seance::class)->find($id);
    
    if (!$seance) {
        throw $this->createNotFoundException('Séance non trouvée');
    }
    $availableSessions = $entityManager->getRepository(Seance::class)->findBy([
        'nom' => $seance->getNom(),  
    ]);

    return $this->render('seances-details.html.twig', [
        'seance' => $seance,
        'availableSessions' => $availableSessions,
    ]);
    }

    #[Route('/home/classes/details/{nom}', name: 'app_classes_details', methods: ['GET'])]
    public function afficheDetailsSeance(string $nom, SeanceRepository $repo): Response
    {
        $seances = $repo->findBy(['nom' => $nom]);
        if (!$seances) {
            $this->addFlash('error', 'Aucune séance trouvée pour ce nom.');
            return $this->redirectToRoute('app_hclasses');
        }
    
        return $this->render('seances-details.html.twig', [
            'nom' => $nom,
            'seances' => $seances,
        ]);
    }

    #[Route('/rate/seance/{id}', name: 'app_rate_seance', methods: ['POST'])]
    public function rate(
        Request $request,
        Seance $seance,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $ratingValue = $data['rating'] ?? null;
        if (!$ratingValue || $ratingValue < 1 || $ratingValue > 5) {
            return new JsonResponse(['message' => 'Évaluation invalide.'], 400);
        }
        $rating = new Rating();
        $rating->setSeance($seance);
        $rating->setRating($ratingValue);

        $entityManager->persist($rating);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Évaluation enregistrée avec succès.']);
    }

}

