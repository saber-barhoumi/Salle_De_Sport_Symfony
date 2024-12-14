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

class ReservationController extends AbstractController
{
    #[Route('/reserver-seance/{id}', name: 'app_reserver_seance', methods: ['POST'])]
    public function reserver(int $id, SeanceRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $seance = $entityManager->createQueryBuilder()
            ->select('s')
            ->from(Seance::class, 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$seance) {
            return $this->redirectToRoute('app_hclasses');
        }
        $currentDate = new DateTime();
        $startOfWeek = clone $currentDate;
        $startOfWeek->setISODate($currentDate->format('Y'), $currentDate->format('W'));   
        $endOfWeek = clone $startOfWeek;
        $endOfWeek->add(new DateInterval('P6D'));
        $seanceDate = $seance->getDate(); 
        
        if ($seanceDate < $startOfWeek) {
            $seance->setStatut('terminée');  
            $entityManager->persist($seance);
            $entityManager->flush();
            $this->addFlash('error', 'La séance est terminée.');
            return $this->redirectToRoute('app_hclasses');
        }
        if ($seance->getStatut() === 'annulée') {
            $this->addFlash('error', 'La séance a été annulée.');
            return $this->redirectToRoute('app_hclasses');
        }
        if ($seance->getParticipantsInscrits() >= $seance->getCapaciteMax()) {
            $this->addFlash('error', 'Désolé, il n\'y a plus de places disponibles pour cette séance.');
            return $this->redirectToRoute('app_hclasses');
        }

        $seance->setParticipantsInscrits($seance->getParticipantsInscrits() + 1);
        $entityManager->persist($seance);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été effectuée avec succès !');
        return $this->redirectToRoute('app_classes_details', ['nom' => $seance->getNom()]);
    }
}

