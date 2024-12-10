<?php
namespace App\Controller;
use App\Repository\ProduitRepository;
use App\Entity\Favoris;
use App\Entity\Produit;
use App\Repository\FavorisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class FavorisController extends AbstractController
{
    private $entityManager;

    // Injection de EntityManagerInterface via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    // Afficher tous les produits avec un bouton pour les ajouter aux favoris
    #[Route('/produits', name: 'produits_liste')]
    public function afficherTousLesProduits(ProduitRepository $produitRepository): \Symfony\Component\HttpFoundation\Response
    {
        // Récupérer tous les produits
        $produits = $produitRepository->findAll();

        // Retourner la vue avec les produits
        return $this->render('favoris/produits_liste.html.twig', [
            'produits' => $produits
        ]);
    }

    // Ajouter un produit aux favoris
    #[Route('/ajouter-favoris/{id}', name: 'ajouter_favoris')]
    public function ajouterAuxFavoris(Produit $produit, Security $security): RedirectResponse
    {
        $user = $security->getUser();

        // Vérifier si le produit est déjà dans les favoris
        $favorisRepository = $this->entityManager->getRepository(Favoris::class);
        $existingFavoris = $favorisRepository->findOneBy([
            'utilisateur' => $user,
            'produit' => $produit
        ]);

        if ($existingFavoris) {
            $this->addFlash('error', 'Ce produit est déjà dans vos favoris.');
            return $this->redirectToRoute('produit_show', ['id' => $produit->getId()]);
        }

        // Ajouter le produit aux favoris
        $favoris = new Favoris();
        $favoris->setProduit($produit);
        $favoris->setUtilisateur($user);

        // Sauvegarder dans la base de données
        $this->entityManager->persist($favoris);
        $this->entityManager->flush();

        $this->addFlash('success', 'Produit ajouté aux favoris !');
        return $this->redirectToRoute('produit_show', ['id' => $produit->getId()]);
    }

    // Afficher les favoris de l'utilisateur
    #[Route('/mes-favoris', name: 'mes_favoris')]
    public function afficherMesFavoris(FavorisRepository $favorisRepository, Security $security)
    {
        $user = $security->getUser();

        // Récupérer les favoris de l'utilisateur
        $favoris = $favorisRepository->findBy(['utilisateur' => $user]);

        return $this->render('favoris/mes_favoris.html.twig', [
            'favoris' => $favoris
        ]);
    }

    // Supprimer un produit des favoris
    #[Route('/supprimer-favoris/{id}', name: 'supprimer_favoris')]
    public function retirerDesFavoris(Produit $produit, Security $security): RedirectResponse
    {
        $user = $security->getUser();

        // Récupérer le produit dans les favoris
        $favorisRepository = $this->entityManager->getRepository(Favoris::class);
        $favoris = $favorisRepository->findOneBy([
            'utilisateur' => $user,
            'produit' => $produit
        ]);

        if ($favoris) {
            $this->entityManager->remove($favoris);
            $this->entityManager->flush();

            $this->addFlash('success', 'Produit retiré des favoris.');
        } else {
            $this->addFlash('error', 'Ce produit n\'est pas dans vos favoris.');
        }

        return $this->redirectToRoute('produit_show', ['id' => $produit->getId()]);
    }
    
    #[Route('/favoris/liste', name: 'liste_favoris')]
    public function listeFavoris(FavorisRepository $favorisRepository, Security $security): JsonResponse
    {
        $user = $security->getUser();
        $favoris = $favorisRepository->findBy(['utilisateur' => $user]);
    
        // Transformer les favoris en un tableau
        $favorisData = array_map(function ($favori) {
            return [
                'id' => $favori->getProduit()->getId(),
                'nom' => $favori->getProduit()->getNom(),
                'prix' => $favori->getProduit()->getPrix(),
                'description' => $favori->getProduit()->getDescription(),
                'image' => $favori->getProduit()->getImage(),
            ];
        }, $favoris);
    
        return $this->json(['favoris' => $favorisData]);
    }
    
    
    
}
