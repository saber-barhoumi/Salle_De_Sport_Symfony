<?php

namespace App\Controller;

use App\Entity\TypeSeance;
use App\Form\TypeSeanceformType;
use App\Repository\TypeSeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;


class SeanceTypeController extends AbstractController
{
    #[Route('/typeseance', name: 'app_typeseance')]
    public function index(): Response
    {
        return $this->render('typeseance/index.html.twig', [
            'controller_name' => 'SeanceTypeController',
        ]);
    } 

#[Route('/typeseance/list', name: 'list_typeseance')]
    public function listtypeseance(TypeSeanceRepository $Seancetype): Response
    {
        $em=$Seancetype->findAll();

        return $this->render('typeseance/list.html.twig', [
            'seancetype' => $em,
        ]);
    }
    #[Route('/typeseance/new', name: 'add_typeseance')]
    public function addtypeseance(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seancetype = new TypeSeance();
        $form = $this->createForm(TypeSeanceformType::class, $seancetype);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($seancetype);
            $entityManager->flush();
            return $this->redirectToRoute('list_typeseance');
            
        }  
        return $this->render('typeseance/nouveau.html.twig',['form'=>$form->createView()]);  
    }

    #[Route('/typeseance/delete/{id}', name: 'delete_typeseance')]
    public function deletetypeseance(int  $id, EntityManagerInterface $entityManager): Response
    {
        
        $seancetype = $entityManager->getRepository(TypeSeance::class)->find($id);

            $entityManager->remove($seancetype);
            $entityManager->flush();
            return $this->redirectToRoute('list_typeseance');
            
        }

        #[Route('/typeseance/modify/{id}', name: 'modify_typeseance')]
        public function modifytypeseance(int $id,Request $request, EntityManagerInterface $entityManager): Response
    {
        $seancetype = $entityManager->getRepository(TypeSeance::class)->find($id);
        $form = $this->createForm(TypeSeanceformType::class, $seancetype);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($seancetype);
            $entityManager->flush();
            return $this->redirectToRoute('list_typeseance');
            
        }  
        return $this->render('typeseance/modify.html.twig',['form'=>$form->createView()]);  
    }
     
        

}