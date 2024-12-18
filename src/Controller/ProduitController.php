<?php

namespace App\Controller;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\UnicodeString; 
use App\Repository\ProduitRepository;
use App\Entity\CategorieProduit;
use App\Form\AdvancedSearchType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Service\CartService;  // Si vous avez un service pour gérer le panier
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\FavorisRepository; // Assurez-vous que ce namespace est correct
use App\Repository\CategorieProduitRepository;  // Assurez-vous que cette ligne est présente
use App\Entity\Cart;
use App\Service\ProduitService;  // Exemple de service injecté
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Bundle\PaginatorBundle\KnpPaginatorInterface;


#[Route('/produit')]
class ProduitController extends AbstractController
{
    
    #[Route('/produits', name: 'produit_front')]
    public function afficherProduitsEtGestion(
        Request $request,
        EntityManagerInterface $em,
        SessionInterface $session,
        FavorisRepository $favorisRepository,
        CategorieProduitRepository $categorieProduitRepository
    ): Response {
        // Vérifier si le panier existe dans la session, sinon le créer
        if (!$session->has('cart')) {
            $session->set('cart', [
                'produits' => [],
                'total' => 0,
            ]);
        }
    
        // Récupérer les produits, tags, favoris et catégories
        $produits = $em->getRepository(Produit::class)->findAll();
        $tags = $em->getRepository(Tag::class)->findAll();
        $favoris = $favorisRepository->findAll();
        $categories = $categorieProduitRepository->findAll();
        $cart = $session->get('cart', []);
    
        // Créer le formulaire de recherche avancée
        $form = $this->createForm(AdvancedSearchType::class);
        $form->handleRequest($request);
    
        // Si le formulaire a été soumis et est valide, filtrer les produits en fonction des critères
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'] ?? null;
            $categorie = $data['CategorieProduit'] ?? null;
            $produits = $em->getRepository(Produit::class)->findByCriteria($nom, $categorie);
        }
    
        // Si un paramètre de catégorie est passé, filtrer les produits par catégorie
        if ($request->query->get('categorie')) {
            $categorieId = $request->query->get('categorie');
            $categorie = $em->getRepository(CategorieProduit::class)->find($categorieId);
            if ($categorie) {
                $produits = $em->getRepository(Produit::class)->findBy(['CategorieProduit' => $categorie]);
            }
        }
    
        // Rendre la réponse avec le formulaire et les produits filtrés
        return $this->render('produit/produitFront.html.twig', [
            'form' => $form->createView(),
            'produits' => $produits,
            'tags' => $tags,
            'cart' => $cart,
            'favoris' => $favoris,
            'categories' => $categories,
        ]);
    }
    



    
    #[Route('/frontproduit', name: 'frontproduit_index')]
    public function AFFICHERFRONT(Request $request, EntityManagerInterface $em, SessionInterface $session, FavorisRepository $favorisRepository , CategorieProduitRepository $categorieProduitRepository): Response
    {   
        // Vérifier si le panier existe dans la session, sinon le créer
        if (!$session->has('cart')) {
            $session->set('cart', [
                'produits' => [], // Aucun produit au départ
                'total' => 0,     // Total à 0
            ]);
        }
        

        // Récupérer tous les produits, tags et favoris
        $produits = $em->getRepository(Produit::class)->findAll();
        $tags = $em->getRepository(Tag::class)->findAll();
        $favoris = $favorisRepository->findAll(); // Utiliser le repository FavorisRepository
        $categories = $categorieProduitRepository->findAll();

        // Récupérer le panier depuis la session
        $cart = $session->get('cart', []);

        // Créer le formulaire de recherche avancée
        $form = $this->createForm(AdvancedSearchType::class);
        $form->handleRequest($request);  // Traiter la requête avec le formulaire

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'] ?? null;
            $categorie = $data['CategorieProduit'] ?? null;

           ;


            // Filtrer les produits en fonction des critères du formulaire
            $produits = $em->getRepository(Produit::class)->findByCriteria($nom, $categorie);
        }

        // Rendre la réponse avec le formulaire et les produits filtrés
        return $this->render('produit/produitFront.html.twig', [
            'form' => $form->createView(),
            'produits' => $produits,
            'tags' => $tags, // Ajouter les tags ici
            'cart' => $cart,
            'favoris' => $favoris, // Ajouter les favoris ici
            'categories' => $categories,

        ]);

 }
 #[Route('/new', name: 'produit_new', methods: ['GET', 'POST'])]
 public function new(Request $request, EntityManagerInterface $em): Response
 {
     $produit = new Produit();
     $form = $this->createForm(ProduitType::class, $produit);
     $form->handleRequest($request);
 
     if ($form->isSubmitted() ){
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
                 // Filtrage des mots interdits dans la description

         
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
public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
{
    $queryBuilder = $em->getRepository(Produit::class)->createQueryBuilder('p')
                       ->orderBy('p.id', 'ASC'); // Classement par ID

    // Filtrage des données si nécessaire
    $prixMin = $request->query->get('prixMin');
    $quantiteMin = $request->query->get('quantiteMin');

    if ($prixMin) {
        // Utiliser la méthode correctement définie dans le repository
        $queryBuilder = $em->getRepository(Produit::class)->findByPrixGreaterThanOrEqual((float)$prixMin);
    }

    if ($quantiteMin) {
        $queryBuilder = $em->getRepository(Produit::class)->findByFilters($prixMin, (int)$quantiteMin);
    }

    // Utilisation du paginator pour paginer les résultats
    $pagination = $paginator->paginate(
        $queryBuilder, // Query à paginer
        $request->query->getInt('page', 1), // Page actuelle, par défaut 1
        5 // Nombre d'éléments par page
    );

    return $this->render('produit/index.html.twig', [
        'produits' => $pagination, // Changez 'pagination' en 'produits'
    ]);
}
 
 #[Route('/show/{id}', name: 'produit_show', methods: ['GET'])]
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
/*
public function indexAction(Request $request, ProduitRepository $produitRepository): Response
{
    // 1. Récupérer le paramètre de tri (par exemple, 'prix', 'date', 'nom', etc.)
    $sortBy = $request->query->get('sortBy', 'nom'); // Tri par défaut sur 'nom'

    // 2. Récupérer la liste des produits triés via le repository
    $produits = $produitRepository->findBySort($sortBy);

    // 3. Récupérer le numéro de la page actuelle (par défaut la page 1)
    $page = $request->query->getInt('page', 1);

    // 4. Nombre de produits par page
    $limit = 10;

    // 5. Calculer les indices pour la pagination
    $start = ($page - 1) * $limit;

    // 6. Diviser les produits en pages
    $totalProduits = count($produits);
    $produitsPagines = array_slice($produits, $start, $limit);

    // 7. Calculer le nombre total de pages
    $totalPages = ceil($totalProduits / $limit);

    // 8. Passer les données de pagination à la vue
    return $this->render('produit/index.html.twig', [
        'produits' => $produitsPagines,   // Produits pour la page actuelle
        'totalPages' => $totalPages,      // Nombre total de pages
        'currentPage' => $page,           // Page actuelle
        'sortBy' => $sortBy,              // Paramètre de tri
    ]);
}
*/
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




#[Route('/statistiques', name: 'statistiques_produits', methods: ['GET'])]
public function statistiquesProduits(ProduitRepository $produitRepository): Response
{
    // Récupérer tous les produits ou d'autres critères
    $produits = $produitRepository->findAll();

    // Vérification si des produits existent
    if (!$produits) {
        throw $this->createNotFoundException('Aucun produit trouvé');
    }

    // Récupérer les statistiques
    $totalProduits = count($produits);
    $prixMoyen = $produitRepository->getAveragePrice();
    $produitMaxPrix = $produitRepository->getMaxPriceProduit();
    $produitMinPrix = $produitRepository->getMinPriceProduit();
    
    // Structurer les données pour les statistiques
    $statistiques = [
        'total_produits' => $totalProduits,
        'prix_moyen' => $prixMoyen,
        'produit_max_prix' => $produitMaxPrix ? $produitMaxPrix['nom'] . ' - ' . $produitMaxPrix['prix'] : null,
        'produit_min_prix' => $produitMinPrix ? $produitMinPrix['nom'] . ' - ' . $produitMinPrix['prix'] : null,
    ];

    // Retourner les statistiques sous forme de réponse
    return $this->render('produit/statistiques.html.twig', [
        'statistiques' => $statistiques
    ]);
}





 













        // Partie Avance


        
    




///hjti bihom
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
    return $this->render('produit/search.html.twig', [
        'form' => $form->createView(),
        'produits' => $produits,  // Passer les résultats à la vue
    ]);
}

//chek fihom 

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


#[Route('/ajouter_aux_favoris/{produitId}', name: 'ajouter_aux_favoris')]
public function ajouterAuxFavoris(int $produitId): Response
{
    // Logique pour ajouter un produit aux favoris
    $produit = $this->getDoctrine()->getRepository(Produit::class)->find($produitId);

    // Ajoutez le produit aux favoris de l'utilisateur connecté
    if ($produit) {
        $user = $this->getUser();
        $user->addFavori($produit);
        $this->getDoctrine()->getManager()->flush();

        // Retourne une réponse avec un message de succès
        $this->addFlash('success', 'Produit ajouté aux favoris avec succès !');
    } else {
        $this->addFlash('error', 'Le produit n\'a pas pu être ajouté.');
    }

    return $this->redirectToRoute('produit_liste');
}

#[Route('/stats-produits', name: 'stats_produits')]
public function statsProduits(EntityManagerInterface $em): Response
{
    // Requête pour récupérer les statistiques par tranche de prix
    $produitsParPrix = $em->createQuery(
        'SELECT 
            CASE
                WHEN p.prix BETWEEN 0 AND 50 THEN \'0-50\'
                WHEN p.prix BETWEEN 51 AND 100 THEN \'51-100\'
                WHEN p.prix BETWEEN 101 AND 200 THEN \'101-200\'
                WHEN p.prix BETWEEN 201 AND 500 THEN \'201-500\'
                ELSE \'500+\'
            END AS tranchePrix,
            COUNT(p.id) AS nombreProduits
         FROM App\Entity\Produit p
         GROUP BY tranchePrix
         ORDER BY tranchePrix'
    )->getResult();

    // Transformation des données pour les adapter à la vue si nécessaire
    $produitsParPrix = array_map(function ($item) {
        return [
            'tranchePrix' => $item['tranchePrix'],
            'nombreProduits' => $item['nombreProduits'],
        ];
    }, $produitsParPrix);

    // Retourne la vue Twig avec les données
    return $this->render('produit/stats.html.twig', [
        'produitsParPrix' => $produitsParPrix,
    ]);
}


}
