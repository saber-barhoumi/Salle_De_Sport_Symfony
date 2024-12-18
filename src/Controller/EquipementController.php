<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\EquipementHistory;
use App\Repository\EquipementHistoryRepository;

class EquipementController extends AbstractController
{
    #[Route('/equipement', name: 'app_equipement')]
    public function index(): Response
    {
        return $this->render('equipement/index.html.twig', [
            'controller_name' => 'EquipementController',
        ]);
    }

    #[Route('/showEquipement', name: 'app_show_equipement')]
    public function showEquipement(EquipementRepository $equipementRepository): Response
    {
        $equipements = $equipementRepository->findAll();

        return $this->render('equipement/showEquipement.html.twig', [
            'equipementList' => $equipements,
        ]);
    }

    #[Route('/addEquipement', name: 'app_add_equipement')]
  // Inside the addEquipement method
  public function addEquipement(Request $request, ManagerRegistry $managerRegistry): Response
  {
      $em = $managerRegistry->getManager();
      $equipement = new Equipement();
      $form = $this->createForm(EquipementType::class, $equipement);
      $form->handleRequest($request);
  
      if ($form->isSubmitted() && $form->isValid()) {
          // Handle file upload
          $uploadedFile = $form->get('photo')->getData(); // Assuming 'photo' is the name of the file input in EquipementType
          if ($uploadedFile) {
              $uploadsDirectory = $this->getParameter('image_directory');
              $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
              $uploadedFile->move($uploadsDirectory, $newFilename);
  
              // Set the new filename on the Equipement entity
              $equipement->setPhoto($newFilename);
          }
  
          // Save the equipment
          $em->persist($equipement);
          $em->flush();
  
          // Log the action
          $history = new EquipementHistory($equipement, 'added', new \DateTime(), 'admin'); // Replace 'admin' with actual user
          $em->persist($history);
          $em->flush();
  
          // Add flash message for successful addition
          $this->addFlash('add', 'Equipment added successfully!');
          return $this->redirectToRoute('app_show_equipement');
      }
  
      return $this->renderForm('equipement/addEquipement.html.twig', [
          'form' => $form,
      ]);
  }
  

    #[Route('/editEquipement/{id}', name: 'app_edit_equipement')]
   // Inside the editEquipement method
public function editEquipement(int $id, Request $request, ManagerRegistry $doctrine, EquipementRepository $equipementRepository): Response
{
    $equipement = $equipementRepository->find($id);

    if (!$equipement) {
        throw $this->createNotFoundException('Equipment not found.');
    }

    $form = $this->createForm(EquipementType::class, $equipement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $doctrine->getManager();
        $entityManager->persist($equipement);
        $entityManager->flush();

        // Log the action
        $history = new EquipementHistory($equipement, 'edited', new \DateTime(), 'admin'); // Replace 'admin' with actual user
        $entityManager->persist($history);
        $entityManager->flush();

        // Add flash message for successful edit
        $this->addFlash('edit', 'Equipment updated successfully!');
        return $this->redirectToRoute('app_show_equipement');
    }

    return $this->renderForm('equipement/editEquipement.html.twig', [
        'form' => $form,
    ]);

    }

    #[Route('/deleteEquipement/{id}', name: 'app_delete_equipement')]
    public function deleteEquipement(int $id, ManagerRegistry $doctrine, EquipementRepository $equipementRepository): Response
{
    // Step 1: Find the equipment
    $equipement = $equipementRepository->find($id);

    if (!$equipement) {
        throw $this->createNotFoundException('Equipment not found.');
    }

    // Step 2: Get the EntityManager
    $entityManager = $doctrine->getManager();

    // Step 3: Log the deletion action in the equipement_history table
    $history = new EquipementHistory(
        $equipement,        // Link to the equipment being deleted
        'deleted',          // Action: "deleted"
        new \DateTime(),    // Current timestamp
        'admin'             // Replace with the actual user
    );

    // Persist the deletion log
    $entityManager->persist($history);

    // Step 4: Delete the equipment
    $entityManager->remove($equipement);

    // Step 5: Apply changes to the database
    $entityManager->flush();

    // Add a flash message for user feedback
    $this->addFlash('delete', 'Equipment deleted successfully!');

    // Redirect to the equipment list
    return $this->redirectToRoute('app_show_equipement');
}
    
    
// src/Controller/EquipementController.php

#[Route('/historiqueEquipement', name: 'app_historique_equipement')]
public function showHistoriqueEquipement(EquipementHistoryRepository $historyRepository): Response
{
    // Fetch all history records
    $historique = $historyRepository->findAll();

    // Render the history in a Twig template
    return $this->render('equipement/historique.html.twig', [
        'historiqueList' => $historique,
    ]);
}







    
// Route to display the list of equipments
#[Route('/frontequipement', name: 'app_frontequipement')]
public function showEquipementList(EquipementRepository $equipementRepository): Response
{
    // Fetch all the equipment records from the database
    $equipements = $equipementRepository->findAll();

    // Pass the equipment list to the template for rendering
    return $this->render('equipement/frontequipement.html.twig', parameters: [
        'equipementList' => $equipements,
    ]);
    
}
#[Route('/statEquipementFournisseur', name: 'app_stat_equipement_fournisseur')]
public function statEquipementFournisseur(EquipementRepository $equipementRepository): Response
{
    // Fetch equipment and group by supplier with a count
    $equipements = $equipementRepository->findAll();
    $stats = [];

    foreach ($equipements as $equipement) {
        $fournisseur = $equipement->getFournisseur();
        if (!isset($stats[$fournisseur])) {
            $stats[$fournisseur] = 0;
        }
        $stats[$fournisseur]++;
    }

    // Pass stats to Twig template
    return $this->render('equipement/statEquipementFournisseur.html.twig', [
        'stats' => $stats,
    ]);
}

}

?>

