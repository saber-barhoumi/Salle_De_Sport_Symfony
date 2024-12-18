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


class FavorisController extends AbstractController
{
   public function __construct(
        FavorisRepository $favorisRepository,
        ProduitRepository $produitRepository,
        EntityManagerInterface $entityManager,
    ) {
        $this->favorisRepository = $favorisRepository;
        $this->produitRepository = $produitRepository;
        $this->entityManager = $entityManager;
    }
    

    

    #[Route('/mes-favoris', name: 'mes_favoris')]
    public function afficherMesFavoris(?UserInterface $user): Response
    {
        // Si l'utilisateur n'est pas connecté, on crée un utilisateur statique
        if (!$user) {
            // If no user is logged in, create a default static user
            $user = new Utilisateur();
            $user->setNom('Saber');
            $user->setPrenom('Brh');
            $user->setEmail('saber.brh@example.com');
            $user->setStatut('active');
            $user->setMotDePasse('Saber123');
            $user->setAge(24);

        }
// Vérifier si l'utilisateur a un ID valide
if (!$user->getId()) {
    // Si pas d'ID, on ne tente pas de récupérer les favoris
    $favoris = [];
} else {
    // Récupérer tous les favoris de l'utilisateur
    $favoris = $this->favorisRepository->findFavorisByUser($user->getId());
}
        // Récupérer tous les produits disponibles
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
        // Récupérer l'utilisateur connecté ou créer un utilisateur statique si non connecté
        $user = $this->security->getUser();
        if (!$user) {
            $user = new Utilisateur();
            $user->setNom('Saber');
            $user->setPrenom('Brh');
            $user->setAge(24);
            $user->setEmail('saber.brh@example.com');
            $user->setStatut('active');
            $user->setMotDePasse('Saber123');
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
public function supprimerFavoris(int $id, ?UserInterface $user): Response
{
    // Si l'utilisateur n'est pas connecté, créer un utilisateur statique
    if (!$user) {
        $user = new Utilisateur();
        $user->setNom('Saber');
        $user->setPrenom('Brh');
        $user->setEmail('saber.brh@example.com');
        $user->setStatut('active');
        $user->setMotDePasse('Saber123');
        $user->setAge(24);
    }

    // Récupérer le produit par son ID
    $produit = $this->produitRepository->find($id);

    if ($produit) {
        // Récupérer les favoris associés au produit et à l'utilisateur
        $favoris = $this->favorisRepository->findOneBy(['produit' => $produit, 'utilisateur' => $user]);

        if ($favoris) {
            // Supprimer le produit des favoris
            $this->entityManager->remove($favoris);
            $this->entityManager->flush();  // S'assurer que la suppression est effectuée

            $this->addFlash('success', 'Produit retiré des favoris.');
        } else {
            $this->addFlash('error', 'Ce produit n\'est pas dans vos favoris.');
        }
    } else {
        $this->addFlash('error', 'Produit introuvable.');
    }

    // Rediriger vers la liste des favoris, qui doit être mise à jour
    return $this->redirectToRoute('mes_favoris');
}
}
