<?php
namespace App\Controller;

use App\Entity\Seance;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use DateInterval;

class ReservationSeanceController extends AbstractController
{
    #[Route('/reserver-seance/{id}', name: 'app_reserver_seance', methods: ['POST'])]
    public function reserver(int $id, SeanceRepository $repo, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour réserver une séance.');
            return $this->redirectToRoute('app_login'); // Remplacez 'login' par la route de votre page de connexion
        }
    
        // Rechercher la séance via le QueryBuilder
        $seance = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Seance::class, 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    
        if (!$seance) {
            $this->addFlash('error', 'Séance introuvable.');
            return $this->redirectToRoute('app_hclasses');
        }
    
        // Vérifier la date et le statut de la séance
        $currentDate = new \DateTime();
        $startOfWeek = clone $currentDate;
        $startOfWeek->setISODate($currentDate->format('Y'), $currentDate->format('W'));
        $endOfWeek = clone $startOfWeek;
        $endOfWeek->add(new \DateInterval('P6D'));
        $seanceDate = $seance->getDate();
    
        if ($seanceDate < $startOfWeek) {
            $seance->setStatut('terminée');
            $entityManager->persist($seance);
            $entityManager->flush();
    
            $this->addFlash('error', 'Cette séance est déjà terminée.');
            return $this->redirectToRoute('app_hclasses');
        }
    
        if ($seance->getStatut() === 'annulée') {
            $this->addFlash('error', 'Cette séance a été annulée.');
            return $this->redirectToRoute('app_hclasses');
        }
    
        if ($seance->getParticipantsInscrits() >= $seance->getCapaciteMax()) {
            $this->addFlash('error', 'Désolé, cette séance est complète.');
            return $this->redirectToRoute('app_hclasses');
        }
    
        // Ajouter l'utilisateur comme participant
        $seance->setParticipantsInscrits($seance->getParticipantsInscrits() + 1);
        $entityManager->persist($seance);
        $entityManager->flush();
    
        $this->addFlash('success', 'Votre réservation a été effectuée avec succès !');
        return $this->redirectToRoute('app_classes_details', ['nom' => $seance->getNom()]);
    }
    
}

