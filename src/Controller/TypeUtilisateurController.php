<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TypeUtilisateurRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\TypeUtilisateur;
use App\Form\TypeUtilisateurType;
use Symfony\Component\HttpFoundation\Request;

class TypeUtilisateurController extends AbstractController
{
    #[Route('/type/utilisateur', name: 'app_type_utilisateur')]
    public function index(): Response
    {
        return $this->render('type_utilisateur/index.html.twig', [
            'type_utilisateurs' => 'TypeUtilisateurController',
        ]);
    }

    #[Route('/afficheTypeUtilisateur', name: 'app_afficheTypeUtilisateur')]
    public function afficheTypeUtilisateur(TypeUtilisateurRepository $rep): Response
    {
        $typeutilisateurs =$rep->findAll();
        return $this->render('type_utilisateur/index.html.twig', [
            'typeutilisateurs' => $typeutilisateurs,
        ]);
    }

    #[Route('/ajoutTypeUtilisateur', name: 'app_ajoutTypeUtilisateur')]
    public function ajoutTypeUtilisateur(ManagerRegistry $doctrine, Request $request): Response
    {
    //instancier un objet
    $typeutilisateur= new TypeUtilisateur();
    //creation du formulaire
    $form = $this->createForm(TypeUtilisateurType::class,$typeutilisateur);
    //recuperer les données saisies au niveau
    $form->handleRequest($request);
    if ($form->isSubmitted())
    {
        $em=$doctrine->getManager();
        $em->persist($typeutilisateur);
        $em->flush();
       return $this->redirectToRoute('app_afficheTypeUtilisateur');
    }
    return $this->renderForm('type_utilisateur/new.html.twig', [
        'form' => $form,
    ]);
    }

    #[Route('/suppTypeUtilisateur/{id}', name: 'app_SuppTypeUtilisateur')]
    public function suppTypeUtilisateur($id,TypeUtilisateurRepository $rep,ManagerRegistry $doctrine): Response
    {
        //récupérer l'auteur à supprimer
        $typeutilisateur=$rep->find($id);
        //action de suppression
        $em=$doctrine->getManager();
        //préparation de la requete de suppression
        $em->remove($typeutilisateur);
        //commit au niveau de la BD
        $em->flush();
        return $this->redirectToRoute('app_afficheTypeUtilisateur');
    }

    #[Route('/modifTypeUtilisateur/{id}', name: 'app_ModifTypeUtilisateur')]
    public function modifTypeUtilisateur($id,TypeUtilisateurRepository $rep,ManagerRegistry $doctrine, Request $request): Response
    {
        //récupérer l'auteur à modifier
        $typeutilisateur=$rep->find($id);
        //creation du formulaire
        $form = $this->createForm(TypeUtilisateurType::class,$typeutilisateur);
        //recuperer les données saisies au niveau
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            $em=$doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('app_afficheTypeUtilisateur');
        }
        return $this->renderForm('type_utilisateur/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
