<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Entity\TypeSeance;
use App\Form\SeanceformType;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;


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
    public function listSeance(SeanceRepository $Seance): Response
    {
        $em=$Seance->findAll();

        return $this->render('seance/list.html.twig', [
            'seance' => $em,
        ]);
    }
    #[Route('/seance/new', name: 'add_seance')]
    public function addSeance(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seance = new Seance();
        $form = $this->createForm(SeanceformType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($seance);
            $entityManager->flush();
            return $this->redirectToRoute('list_seance');
            
        }  
        return $this->render('seance/nouveau.html.twig',['form'=>$form->createView()]);  
    }

    #[Route('/seance/delete/{id}', name: 'delete_seance')]
    public function deleteSeance(int  $id, EntityManagerInterface $entityManager): Response
    {
        
        $seance = $entityManager->getRepository(Seance::class)->find($id);

            $entityManager->remove($seance);
            $entityManager->flush();
            return $this->redirectToRoute('list_seance');
            
        }

        #[Route('/seance/modify/{id}', name: 'modify_seance')]
        public function modifySeance(int $id,Request $request, EntityManagerInterface $entityManager): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->find($id);
        $form = $this->createForm(SeanceformType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($seance);
            $entityManager->flush();
            return $this->redirectToRoute('list_seance');
            
        }  
        return $this->render('seance/modify.html.twig',['form'=>$form->createView()]);  
    }
     
        

}
