<?php

namespace App\Controller;

use App\Entity\Typeabonnement;
use App\Form\TypeabonnementType;
use App\Repository\TypeabonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/typeabonnement')]
final class TypeabonnementController extends AbstractController
{
    #[Route(name: 'app_typeabonnement_index', methods: ['GET'])]
    public function index(TypeabonnementRepository $typeabonnementRepository): Response
    {
        return $this->render('typeabonnement/index.html.twig', [
            'typeabonnements' => $typeabonnementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_typeabonnement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeabonnement = new Typeabonnement();
        $form = $this->createForm(TypeabonnementType::class, $typeabonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($typeabonnement);
                $entityManager->flush();
                $this->addFlash('success', 'Le type d\'abonnement a été créé avec succès.');

                return $this->redirectToRoute('app_typeabonnement_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            }
        }

        return $this->render('typeabonnement/new.html.twig', [
            'typeabonnement' => $typeabonnement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_typeabonnement_show', methods: ['GET'])]
    public function show(Typeabonnement $typeabonnement): Response
    {
        return $this->render('typeabonnement/show.html.twig', [
            'typeabonnement' => $typeabonnement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_typeabonnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Typeabonnement $typeabonnement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeabonnementType::class, $typeabonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Le type d\'abonnement a été modifié avec succès.');
                
                return $this->redirectToRoute('app_typeabonnement_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            }
        }

        return $this->render('typeabonnement/edit.html.twig', [
            'typeabonnement' => $typeabonnement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_typeabonnement_delete', methods: ['POST'])]
    public function delete(Request $request, Typeabonnement $typeabonnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeabonnement->getId(), $request->get('_token'))) {
            $entityManager->remove($typeabonnement);
            $entityManager->flush();
            $this->addFlash('success', 'Le type d\'abonnement a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_typeabonnement_index', [], Response::HTTP_SEE_OTHER);
    }
}
