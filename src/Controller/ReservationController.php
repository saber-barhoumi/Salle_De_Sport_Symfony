<?php

namespace App\Controller;

use App\Repository\CategorieEquipementRepository;
use App\Repository\EquipementRepository;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Dompdf\Options ;
use Dompdf\Dompdf ;

class ReservationController extends AbstractController
{
    // Route to display the list of reservations
    #[Route('/reservation', name: 'reservation_index')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        // Get the logged-in user   
        $utilisateur = $this->getUser();

        // Fetch reservations for the logged-in user
        $reservations = $reservationRepository->findBy(['utilisateur' => $utilisateur]);

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    // Route to create a new reservation
    #[Route('/reservation/new', name: 'reservation_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = $this->getUser(); // Get the logged-in user
        $reservation = new Reservation();
        $reservation->setUtilisateur($utilisateur); // Associate the reservation with the user
        
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reservation);
            $em->flush();
            $this->sendTwilioMessage($reservation);
            $this->addFlash('success', 'Reservation created successfully!');
            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route to delete a reservation
    #[Route('/reservation/delete/{id}', name: 'reservation_delete', methods: ['POST', 'GET'])]
    public function delete(Reservation $reservation, EntityManagerInterface $em): Response
    {
        $utilisateur = $this->getUser();

        // Ensure the logged-in user owns the reservation
        if ($reservation->getUtilisateur() !== $utilisateur) {
            $this->addFlash('error', 'You are not authorized to delete this reservation.');
            return $this->redirectToRoute('reservation_index');
        }

        // Remove the reservation
        $em->remove($reservation);
        $em->flush();

        $this->addFlash('success', 'Reservation deleted successfully.');
        return $this->redirectToRoute('reservation_index');
    }
    #[Route('/reservation/statistics', name: 'reservation_statistics')]
    public function statistics(ReservationRepository $reservationRepository): Response
    {
        // Récupérer les réservations groupées par équipement
        $stats = $reservationRepository->createQueryBuilder('r')
            ->select('e.nom AS equipement', 'COUNT(r.id) AS reservation_count')
            ->leftJoin('r.equipement', 'e') // Joindre les équipements
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();

        // Passer les résultats au template
        return $this->render('reservation/statistics.html.twig', [
            'stats' => $stats
        ]);
    }


    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    private function sendTwilioMessage(Reservation $reservation): void
    {
        $twilioAccountSid = $this->getParameter('twilio_account_sid');
        $twilioAuthToken = $this->getParameter('twilio_auth_token');
        $twilioPhoneNumber = $this->getParameter('twilio_phone_number');

        $twilioClient = new Client($twilioAccountSid, $twilioAuthToken);

        // Replace 'to' with the recipient phone number
        // Replace 'from' with your Twilio phone number
        $twilioClient->messages->create(
            '+21696759307', // Replace with the recipient's phone number
            [
                'from' => $twilioPhoneNumber,
                'body' => 'Your Reservation has been confirmed ' . $reservation->getUtilisateur(),
            ]
        );
    }
    #[Route('/reservation/pdf/{id}', name: 'app_reservation_pdf')]
    public function generatePdf(Reservation $reservation): Response
    {
        // Fetch reservation details and pass them to the PDF template
        $html = $this->renderView('reservation/reservationpdf.html.twig', [
            'reservation' => $reservation,
        ]);

        // Configure Dompdf options
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', false); // Disable PHP execution inside HTML

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);

        // Set paper size (A4)
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Stream the generated PDF back to the user
        $output = $dompdf->output();

        $response = new Response($output);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    
  #[Route('/reservation/history', name: 'reservation_history')]
  public function reservationHistory(EntityManagerInterface $em): Response
  {
      // Fetch all reservations
      $reservations = $em->getRepository(Reservation::class)->findAll();
  
      return $this->render('reservation/history.html.twig', [
          'reservations' => $reservations,
      ]);
  }

  #[Route('/statistics', name: 'app_statistics')]
public function indexx(
    CategorieEquipementRepository $categorieEquipementRepository,
    EquipementRepository $equipementRepository,
    ReservationRepository $reservationRepository
): Response {
    // 1. Category Equipment Stats
    $categoryStats = $categorieEquipementRepository->findCategoryStatistics();
    $formattedCategoryStats = [];
    foreach ($categoryStats as $stat) {
        $formattedCategoryStats[$stat['categoryName']] = $stat['equipCount'];
    }

    // 2. Equipment by Supplier Stats
    $equipements = $equipementRepository->findAll();
    $supplierStats = [];
    foreach ($equipements as $equipement) {
        $fournisseur = $equipement->getFournisseur();
        if (!isset($supplierStats[$fournisseur])) {
            $supplierStats[$fournisseur] = 0;
        }
        $supplierStats[$fournisseur]++;
    }

    // 3. Reservation Stats grouped by Equipment/User
    $reservationStats = $reservationRepository->createQueryBuilder('r')
        ->select('u.nom AS user', 'e.nom AS equipement', 'COUNT(r.id) AS reservation_count')
        ->leftJoin('r.equipement', 'e')
        ->leftJoin('r.utilisateur', 'u')
        ->groupBy('u.id, e.id')
        ->getQuery()
        ->getResult();

    // Group reservation stats by user
    $groupedReservationStats = [];
    foreach ($reservationStats as $stat) {
        $groupedReservationStats[$stat['user']][] = [
            'equipement' => $stat['equipement'],
            'reservation_count' => $stat['reservation_count'],
        ];
    }

    return $this->render('reservation/equipment_stats.html.twig', [
        'categoryStats' => $formattedCategoryStats,
        'supplierStats' => $supplierStats,
        'groupedStats' => $groupedReservationStats,
    ]);
}

  
    
}