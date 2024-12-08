<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\UnicodeString; 
use App\Repository\ProduitRepository;
use App\Entity\CategorieProduit;
use App\Form\AdvancedSearchType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\CartService;  // Si vous avez un service pour gérer le panier
use Symfony\Component\HttpFoundation\Session\SessionInterface;


#[Route('/produit')]
class ProduitController extends AbstractController
{
    
    
   #[Route('/frontproduit', name: 'frontproduit_index')]
public function AFFICHERFRONT(Request $request, EntityManagerInterface $em, SessionInterface $session): Response
{   
    if (!$session->has('cart')) {
        $session->set('cart', [
            'produits' => [], // Aucun produit au départ
            'total' => 0,     // Total à 0
        ]);
    }
    // Récupérer tous les produits et tous les tags
    $produits = $em->getRepository(Produit::class)->findAll();
    $tags = $em->getRepository(Tag::class)->findAll();

    // Récupérer le panier depuis la session
    $cart = $session->get('cart', []);

    // Créer le formulaire de recherche
    $form = $this->createForm(AdvancedSearchType::class);
    $form->handleRequest($request);  // Traiter la requête avec le formulaire
    
    // Vérifier si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $nom = $data['nom'] ?? null;
        $categorie = $data['CategorieProduit'] ?? null;

        // Filtrer les produits en fonction des données du formulaire (si elles existent)
        $produits = $em->getRepository(Produit::class)->findByCriteria($nom, $categorie);
    }

    // Rendu de la réponse avec le formulaire et les produits filtrés
    return $this->render('produit/produitFront.html.twig', [
        'form' => $form->createView(),
        'produits' => $produits,
        'tags' => $tags, // Ajouter les tags ici
        'cart' => $cart,
    ]);

    
      
 }
 #[Route('/new', name: 'produit_new', methods: ['GET', 'POST'])]
 public function new(Request $request, EntityManagerInterface $em): Response
 {
     $produit = new Produit();
     $form = $this->createForm(ProduitType::class, $produit);
     $form->handleRequest($request);
 
     if ($form->isSubmitted() && $form->isValid()) {
         // Gestion de l'image
         $imageFile = $form->get('image')->getData();
         if ($imageFile) {
             $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
 
             // Sécurisation du nom de fichier
             $safeFilename = (new UnicodeString($originalFilename))
                 ->ascii()
                 ->toString();
 
             $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
 
             try {
                 $imageFile->move(
                     $this->getParameter('images_directory'), 
                     $newFilename
                 );
             } catch (FileException $e) {
                 $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                 return $this->redirectToRoute('produit_new');
             }
 
             $produit->setImage($newFilename);
         }
 
         // Gestion des tags
         foreach ($produit->getTags() as $tag) {
             // Vérifier si le tag existe déjà
             $existingTag = $em->getRepository(Tag::class)->findOneBy(['nom' => $tag->getNom()]);
             if ($existingTag) {
                 $produit->removeTag($tag); // Supprime le tag temporaire
                 $produit->addTag($existingTag); // Associe le tag existant
             } else {
                 $em->persist($tag); // Persiste un nouveau tag
             }
         }
 
         // Persistance du produit
         $em->persist($produit);
         $em->flush();
 
         $this->addFlash('success', 'Produit ajouté avec succès !');
         return $this->redirectToRoute('produit_index');
     }
 
     return $this->render('produit/new.html.twig', [
         'form' => $form->createView(),
     ]);
 }
 
    
    #[Route('/', name: 'produit_index', methods: ['GET'])]
 public function index(EntityManagerInterface $em): Response
 {
    $produits = $em->getRepository(Produit::class)->findAll();

    return $this->render('produit/index.html.twig', [
        'produits' => $produits,
    ]);
 }      
 #[Route('/{id}', name: 'produit_show', methods: ['GET'])]
public function show($id, EntityManagerInterface $em): Response
{
    if (!ctype_digit($id)) { // Vérifie si $id est composé uniquement de chiffres
        throw $this->createNotFoundException('Invalid product ID');
    }

    $id = (int) $id; // Convertir la chaîne en entier
    $produit = $em->getRepository(Produit::class)->find($id);

    if (!$produit) {
        throw $this->createNotFoundException('No product found for id ' . $id);
    }

    $categorie = $produit->getCategorieProduit();

    return $this->render('produit/show.html.twig', [
        'produit' => $produit,
        'categorie' => $categorie,
    ]);
}

 
 #[Route('/{id}/delete', name: 'produit_delete', methods: ['POST'])]
 public function delete(Request $request, Produit $produit, EntityManagerInterface $em): Response
 {
     if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
         $em->remove($produit);
         $em->flush();
         $this->addFlash('success', 'Produit supprimé avec succès.');
     }
 
     return $this->redirectToRoute('produit_index');
 }

 #[Route('/produit/categorie/{categorieId<\d+>}/produit/{id}/edit', name: 'produit_edit', methods: ['GET', 'POST'])]
public function editProduitByCategorie(
    string $categorieId,
    int $id,
    ProduitRepository $produitRepository,
    Request $request,
    EntityManagerInterface $em
): Response {
    // Récupération de la catégorie
    $categorie = $em->getRepository(CategorieProduit::class)->find($categorieId);
    if (!$categorie) {
        throw $this->createNotFoundException('Catégorie non trouvée pour l\'ID ' . $categorieId);
    }

    // Récupération du produit
    $produit = $produitRepository->findOneBy(['id' => $id, 'CategorieProduit' => $categorie]);
    if (!$produit) {
        throw $this->createNotFoundException('Produit non trouvé dans cette catégorie');
    }

    // Création et gestion du formulaire
    $form = $this->createForm(ProduitType::class, $produit);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gestion de l'image
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

            // Sécurisation du nom de fichier
            $safeFilename = (new UnicodeString($originalFilename))
                ->ascii()
                ->toString();

            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('images_directory'), 
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                return $this->redirectToRoute('produit_edit', [
                    'categorieId' => $categorieId,
                    'id' => $id,
                ]);
            }

            $produit->setImage($newFilename);
        }

        // Gestion des tags
        $submittedTags = $produit->getTags();
        foreach ($submittedTags as $tag) {
            // Vérifier si le tag existe déjà
            $existingTag = $em->getRepository(Tag::class)->findOneBy(['nom' => $tag->getNom()]);
            if ($existingTag) {
                $produit->removeTag($tag); // Supprime le tag temporaire
                $produit->addTag($existingTag); // Associe le tag existant
            } else {
                $em->persist($tag); // Persiste un nouveau tag
            }
        }

        $em->flush();

        $this->addFlash('success', 'Produit et tags modifiés avec succès.');
        return $this->redirectToRoute('produit_index');
    }

    return $this->render('produit/edit.html.twig', [
        'form' => $form->createView(),
        'produit' => $produit,
        'categorie' => $categorie,
    ]);
}
#[Route('/{id}/statistiques', name: 'statistiques_produits', methods: ['GET'])]
    public function statistiquesProduits(ProduitRepository $produitRepository): JsonResponse
    {
        // Récupérer les statistiques des produits
        $totalProduits = $produitRepository->count([]);
        $prixMoyen = $produitRepository->getAveragePrice();
        $produitMaxPrix = $produitRepository->getMaxPriceProduit();
        $produitMinPrix = $produitRepository->getMinPriceProduit();
        dump($totalProduits, $prixMoyen, $produitMaxPrix, $produitMinPrix);

        // Structurer les données pour les statistiques
        $statistiques = [
            'total_produits' => $totalProduits,
            'prix_moyen' => $prixMoyen,
            'produit_max_prix' => $produitMaxPrix ? $produitMaxPrix['nom'] . ' - ' . $produitMaxPrix['prix'] : null,
            'produit_min_prix' => $produitMinPrix ? $produitMinPrix['nom'] . ' - ' . $produitMinPrix['prix'] : null,
        ];

        // Retourner les statistiques sous forme de réponse JSON
        return new JsonResponse($statistiques);
    }
 
#[Route('/tag/{id}/produits', name: 'produits_by_tag', methods: ['GET'])]
public function produitsByTag(int $id, TagRepository $tagRepository): JsonResponse
{
    // Trouver le tag par son ID
    $tag = $tagRepository->find($id);

    if (!$tag) {
        return new JsonResponse(['error' => 'Tag introuvable.'], 404);
    }

    // Récupérer les produits associés à ce tag
    $produits = $tag->getProduits();

    // Transformer les produits en un tableau JSON
    $produitsData = [];
    foreach ($produits as $produit) {
        $produitsData[] = [
            'nom' => $produit->getNom(),
            'prix' => $produit->getPrix(),
        ];
    }

    return new JsonResponse($produitsData);
}




#[Route('/produits/filtrer', name: 'produit_filtrer', methods: ['GET'])]
public function filtrerProduits(ProduitRepository $produitRepository, Request $request): Response
{
    // Récupération des valeurs du formulaire
    $prixMin = $request->query->get('prix_min', 0); // 0 par défaut
    $prixMax = $request->query->get('prix_max', PHP_INT_MAX); // Valeur maximale par défaut

    // Recherche des produits par plage de prix
    $produits = $produitRepository->findByPriceRange((float) $prixMin, (float) $prixMax);

    // Rendu de la vue
    return $this->render('produit/produitFront.html.twig', [
        'produits' => $produits,
    ]);
}







        // Partie Avance


        #[Route('/produitFront/search', name: 'produit_front_search', methods: ['GET', 'POST'])]
        public function produitFrontSearch(Request $request, ProduitRepository $produitRepository): Response
        {
            // Créer le formulaire de recherche
            $form = $this->createForm(AdvancedSearchType::class);
            $form->handleRequest($request);
        
            $produits = [];
        
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
        
                // Rechercher les produits en fonction des critères
                $nom = $data['nom'] ?? null;
                $categorie = $data['CategorieProduit'] ?? null;
        
                $produits = $produitRepository->findByCriteria($nom, $categorie);
            }
        
            // Rendu de la vue avec les résultats
            return $this->render('produit_front/search.html.twig', [
                'form' => $form->createView(),
                'produits' => $produits,
            ]);
        }
        
    
// src/Controller/ProduitController.php
#[Route('/recherche', name: 'produit_recherche', methods: ['GET', 'POST'])]
public function rechercheAvancee(Request $request, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
{
    // Création du formulaire
    $form = $this->createForm(AdvancedSearchType::class);
    $form->handleRequest($request);

    $produits = [];  // Initialisation de la variable produits

    // Vérification si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer les critères du formulaire
        $criteria = $form->getData();

        $categorieProduit = null;
        if (!empty($criteria['CategorieProduit'])) {
            $categorieProduit = $entityManager->getRepository(CategorieProduit::class)
                                              ->find($criteria['CategorieProduit']);
        }

        // Effectuer la recherche avec les critères
        $produits = $produitRepository->findByCriteria(
            $criteria['nom'] ?? null,  // Si le champ 'nom' est vide, il ne sera pas utilisé
            $categorieProduit
        );
    }

    // Rendre la vue avec les résultats de la recherche
    return $this->render('produit/recherche.html.twig', [
        'form' => $form->createView(),
        'produits' => $produits,  // Passer les résultats à la vue
    ]);
}

#[Route('/edit-produit/{id}', name: 'edit_produit')]
public function editProduit(Produit $produit, Request $request, EntityManagerInterface $em): Response
{
    // Récupérer tous les tags
    $tags = $em->getRepository(Tag::class)->findAll();

    // Créer le formulaire pour éditer le produit et ajouter des tags
    $form = $this->createForm(ProduitType::class, $produit, [
        'tags' => $tags
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Sauvegarder les changements dans la base de données
        $em->flush();

        return $this->redirectToRoute('produit_success');
    }

    return $this->render('produit/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}




}
