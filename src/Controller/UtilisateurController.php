<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UtilisateurRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Component\HttpFoundation\Request;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function index(UtilisateurRepository $rep): Response
    {
        $utilisateurs =$rep->findAll();
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/ajoutUtilisateur', name: 'app_ajoutUtilisateur')]
    public function ajoutUtilisateur(ManagerRegistry $doctrine, Request $request): Response
    {
    //instancier un objet
    $utilisateur= new Utilisateur();
    //creation du formulaire
    $form = $this->createForm(UtilisateurType::class,$utilisateur);
    //recuperer les données saisies au niveau
    $form->handleRequest($request);
    if ($form->isSubmitted())
    {
        $em=$doctrine->getManager();
        $em->persist($utilisateur);
        $em->flush();
       return $this->redirectToRoute('app_utilisateur');
    }
    return $this->renderForm('utilisateur/ajoutUtilisateur.html.twig', [
        'form' => $form->createView(),
    ]);
    }

    #[Route('/suppUtilisateur/{id}', name: 'app_SuppUtilisateur')]
    public function suppUtilisateur($id,UtilisateurRepository $rep,ManagerRegistry $doctrine): Response
    {
        //récupérer l'auteur à supprimer
        $utilisateur=$rep->find($id);
        //action de suppression
        $em=$doctrine->getManager();
        //préparation de la requete de suppression
        $em->remove($utilisateur);
        //commit au niveau de la BD
        $em->flush();
        return $this->redirectToRoute('app_utilisateur');
    }

    #[Route('/modifUtilisateur/{id}', name: 'app_ModifUtilisateur')]
    public function modifUtilisateur($id,UtilisateurRepository $rep,ManagerRegistry $doctrine, Request $request): Response
    {
        //récupérer l'auteur à modifier
        $utilisateur=$rep->find($id);
        //creation du formulaire
        $form = $this->createForm(UtilisateurType::class,$utilisateur);
        //recuperer les données saisies au niveau
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            $em=$doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('app_utilisateur');
        }
        return $this->renderForm('utilisateur/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/utilisateur/{id}', name: 'app_utilisateur_show')]
public function showUtilisateur(UtilisateurRepository $rep, int $id): Response
{
    $utilisateur = $rep->find($id);

    if (!$utilisateur) {
        throw $this->createNotFoundException('Utilisateur non trouvé.');
    }

    return $this->render('utilisateur/showUser.html.twig', [
        'utilisateur' => $utilisateur,
    ]);
}

#[Route('/stats', name: 'stats_utilisateurs')]
    public function statsUtilisateurs(EntityManagerInterface $em): Response
    {
        // Exemple de requête pour récupérer les statistiques par tranche d'âge
        $utilisateursParAge = $em->createQuery(
            'SELECT 
                CASE
                    WHEN u.age BETWEEN 0 AND 17 THEN \'0-17\'
                    WHEN u.age BETWEEN 18 AND 25 THEN \'18-25\'
                    WHEN u.age BETWEEN 26 AND 35 THEN \'26-35\'
                    WHEN u.age BETWEEN 36 AND 50 THEN \'36-50\'
                    ELSE \'50+\'
                END AS trancheAge,
                COUNT(u.id) AS nombreUtilisateurs
             FROM App\Entity\Utilisateur u
             GROUP BY trancheAge
             ORDER BY trancheAge'
        )->getResult();

        // Exemple de requête pour récupérer les statistiques par genre
        $utilisateursParGenre = $em->createQuery(
            'SELECT 
                u.genre AS genre,
                COUNT(u.id) AS nombreUtilisateurs
             FROM App\Entity\Utilisateur u
             GROUP BY u.genre'
        )->getResult();

        // Transformation des données pour les adapter à la vue si nécessaire
        $utilisateursParAge = array_map(function ($item) {
            return [
                'trancheAge' => $item['trancheAge'],
                'nombreUtilisateurs' => $item['nombreUtilisateurs'],
            ];
        }, $utilisateursParAge);

        $utilisateursParGenre = array_map(function ($item) {
            return [
                'genre' => $item['genre'] ?? 'Non spécifié',
                'nombreUtilisateurs' => $item['nombreUtilisateurs'],
            ];
        }, $utilisateursParGenre);

        // Retourne la vue Twig avec les données
        return $this->render('utilisateur/stats.html.twig', [
            'utilisateursParAge' => $utilisateursParAge,
            'utilisateursParGenre' => $utilisateursParGenre,
        ]);
    }
}



    




    


