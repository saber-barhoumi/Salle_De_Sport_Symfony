<?php
namespace App\Controller;

use App\Form\SearchSeanceType;
use App\Repository\SeanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeanceSearchController extends AbstractController
{
    #[Route('/seance/search', name: 'search_seance')]
    public function search(Request $request, SeanceRepository $seanceRepository): Response
    {
        $form = $this->createForm(SearchSeanceType::class);
        $form->handleRequest($request);
        $sortField = 'date';
        $sortOrder = 'ASC';
    
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            switch ($data['sortBy']) {
                case 'typeSeance':
                    $sortField = 'typeSeance.type';
                    break;
                case 'date':
                default:
                    $sortField = 'date';
                    break;
            }
    
            $sortOrder = $data['sortOrder'] ?? 'ASC';
        }
    
        $seances = $seanceRepository->findByCriteria($sortField, $sortOrder);
    
        return $this->render('seance/list.html.twig', [
            'form' => $form->createView(),
            'seances' => $seances,
        ]);
    }
    
    
}
