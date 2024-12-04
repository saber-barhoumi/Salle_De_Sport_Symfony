<?php

namespace App\Controller;

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

    #[Route('/afficheUtilisateurs', name: 'app_afficheUtilisateurs')]
    public function afficheUtilisateur(UtilisateurRepository $rep): Response
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
    
}

