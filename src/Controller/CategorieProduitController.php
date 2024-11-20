<?php

namespace App\Controller;

use App\Entity\Produit;  // Ajoutez cette ligne pour importer l'entité Produit
use App\Entity\CategorieProduit;
use App\Form\CategorieProduitType;
use App\Repository\CategorieProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categorie/produit')]
final class CategorieProduitController extends AbstractController
{
    #[Route(name: 'categorie_produit_index', methods: ['GET'])]
    public function index(CategorieProduitRepository $categorieProduitRepository): Response
    {
        return $this->render('categorie_produit/index.html.twig', [
            'categorie_produits' => $categorieProduitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'categorie_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    { 
         // Ajouter un message flash pour informer l'utilisateur
        $this->addFlash('success', 'Catégorie ajoutée avec succès !');

        $categorieProduit = new CategorieProduit();
        $form = $this->createForm(CategorieProduitType::class, $categorieProduit);
        $form->handleRequest($request);

        // Ajoutez un dump() ici pour vérifier le contenu du formulaire
    dump($form->createView()); // Ceci vous montre la vue du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieProduit);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie_produit/new.html.twig', [
            'categorie_produit' => $categorieProduit,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}', name: 'categorie_produit_show', methods: ['GET'])]
    public function show(CategorieProduit $categorieProduit): Response
    {
        return $this->render('categorie_produit/show.html.twig', [
            'categorie_produit' => $categorieProduit,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'categorie_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieProduit $categorieProduit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieProduitType::class, $categorieProduit);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            return $this->redirectToRoute('categorie_produit_index');
        }
    
        return $this->render('categorie_produit/edit.html.twig', [
            'categorie_produit' => $categorieProduit,
            'form' => $form->createView(),
        ]);
    }
    
    // Si vous voulez supprimer un objet CategorieProduit/**
    #[Route('/{id}/delete', name: 'categorie_produit_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieProduit $categorieProduit, EntityManagerInterface $entityManager): Response
    {
        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete' . $categorieProduit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categorieProduit);
            $entityManager->flush();
        }
    
        // Redirection après suppression
        return $this->redirectToRoute('categorie_produit_index');
    }




}
