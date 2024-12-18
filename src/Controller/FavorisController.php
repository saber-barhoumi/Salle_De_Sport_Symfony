<?php

namespace App\Controller;

use App\Entity\Utilisateur;  // Utilisez Utilisateur ici au lieu de User
use App\Entity\Favoris;
use App\Repository\FavorisRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;


class FavorisController extends AbstractController
{
    private $favorisRepository;
    private $produitRepository;
    private $entityManager;
    private $security;

   public function __construct(
        FavorisRepository $favorisRepository,
        ProduitRepository $produitRepository,
        EntityManagerInterface $entityManager,
        Security $security // Injection du service Security
        
    ) {
        $this->favorisRepository = $favorisRepository;
        $this->produitRepository = $produitRepository;
        $this->entityManager = $entityManager;
        $this->security = $security; // Initialisation de la propriété
    }
    

    

    #[Route('/mes-favoris', name: 'mes_favoris')]
    public function afficherMesFavoris(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
    
        if (!$user) {
            // Rediriger ou afficher un message d'erreur si l'utilisateur n'est pas connecté
            $this->addFlash('error', 'Vous devez être connecté pour accéder à vos favoris.');
            return $this->redirectToRoute('app_login'); // Remplacez 'login' par la route de votre page de connexion
        }
    
        // Récupérer tous les favoris de l'utilisateur connecté
        $favoris = $this->favorisRepository->findBy(['utilisateur' => $user]);
    
        // Récupérer tous les produits disponibles (optionnel)
        $produits = $this->produitRepository->findAll();
    
        // Retourner la vue avec les favoris et les produits disponibles
        return $this->render('favoris/mes_favoris.html.twig', [
            'favoris' => $favoris,
            'produits' => $produits,
        ]);
    }
    

    
    #[Route('/ajouter-favoris/{id}', name: 'ajouter_favoris')]
public function ajouterAuxFavoris(int $id): Response
{
    // Récupérer l'utilisateur connecté
    $user = $this->getUser();

    if (!$user) {
        // Rediriger ou afficher un message si aucun utilisateur n'est connecté
        $this->addFlash('error', 'Vous devez être connecté pour ajouter un produit à vos favoris.');
        return $this->redirectToRoute('app_login'); // Remplacez 'login' par la route vers votre page de connexion
    }

    // Récupérer le produit par son ID
    $produit = $this->produitRepository->find($id);
    
    if (!$produit) {
        $this->addFlash('error', 'Produit introuvable.');
        return $this->redirectToRoute('mes_favoris');
    }

    // Vérifier si le produit est déjà dans les favoris
    $favorisExistant = $this->favorisRepository->findOneBy([
        'utilisateur' => $user,
        'produit' => $produit,
    ]);

    if ($favorisExistant) {
        $this->addFlash('info', 'Ce produit est déjà dans vos favoris.');
        return $this->redirectToRoute('mes_favoris');
    }

    // Ajouter le produit aux favoris
    $favoris = new Favoris();
    $favoris->setProduit($produit);
    $favoris->setUtilisateur($user);

    $this->entityManager->persist($favoris);
    $this->entityManager->flush();

    // Ajouter un message flash pour l'utilisateur
    $this->addFlash('success', 'Produit ajouté aux favoris.');

    // Rediriger vers la liste des favoris
    return $this->redirectToRoute('mes_favoris');
}

    
    

#[Route('/supprimer-favoris/{id}', name: 'supprimer_favoris')]
public function supprimerFavoris(int $id): Response
{
    // Récupérer l'utilisateur connecté
    $user = $this->getUser();

    // Vérifier si l'utilisateur est connecté
    if (!$user) {
        $this->addFlash('error', 'Vous devez être connecté pour supprimer un produit de vos favoris.');
        return $this->redirectToRoute('app_login'); // Remplacez 'login' par le nom de la route de votre page de connexion
    }

    // Récupérer le produit par son ID
    $produit = $this->produitRepository->find($id);

    if (!$produit) {
        $this->addFlash('error', 'Produit introuvable.');
        return $this->redirectToRoute('mes_favoris');
    }

    // Récupérer le favori associé au produit et à l'utilisateur connecté
    $favoris = $this->favorisRepository->findOneBy([
        'produit' => $produit,
        'utilisateur' => $user,
    ]);

    if (!$favoris) {
        $this->addFlash('error', 'Ce produit n\'est pas dans vos favoris.');
    } else {
        // Supprimer le favori
        $this->entityManager->remove($favoris);
        $this->entityManager->flush();

        $this->addFlash('success', 'Produit retiré des favoris.');
    }

    // Rediriger vers la liste des favoris
    return $this->redirectToRoute('mes_favoris');
}

}
