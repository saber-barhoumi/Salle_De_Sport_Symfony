<?php
namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse; // Importation de RedirectResponse
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    #[Route('/tag/new', name: 'tag_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();

            return $this->redirectToRoute('tag_list');
        }

        return $this->render('tag/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tags', name: 'tag_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $tags = $em->getRepository(Tag::class)->findAll();

        return $this->render('tag/list.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/tag/delete/{id}', name: 'tag_delete')]
    public function delete(EntityManagerInterface $em, $id): RedirectResponse
    {
        // Récupérer le tag à partir de l'id
        $tag = $em->getRepository(Tag::class)->find($id);

        if (!$tag) {
            // Si le tag n'existe pas, rediriger avec un message d'erreur
            $this->addFlash('error', 'Tag introuvable.');
            return $this->redirectToRoute('tag_list');
        }

        // Supprimer le tag
        $em->remove($tag);
        $em->flush();

        // Ajouter un message flash de succès
        $this->addFlash('success', 'Tag supprimé avec succès.');

        // Rediriger vers la liste des tags
        return $this->redirectToRoute('tag_list');
    }
     // Route pour modifier un tag existant
     #[Route('/tag/edit/{id}', name: 'tag_edit')]
     public function edit(Request $request, EntityManagerInterface $em, $id): Response
     {
         // Récupérer le tag à partir de l'id
         $tag = $em->getRepository(Tag::class)->find($id);
 
         if (!$tag) {
             // Si le tag n'existe pas, rediriger vers la liste avec un message d'erreur
             $this->addFlash('error', 'Tag introuvable.');
             return $this->redirectToRoute('tag_list');
         }
 
         // Créer un formulaire pour éditer le tag
         $form = $this->createForm(TagType::class, $tag);
         $form->handleRequest($request);
 
         // Vérifier si le formulaire est soumis et valide
         if ($form->isSubmitted() && $form->isValid()) {
             // Sauvegarder les modifications dans la base de données
             $em->flush();
 
             // Ajouter un message flash de succès
             $this->addFlash('success', 'Tag mis à jour avec succès.');
 
             // Rediriger vers la liste des tags
             return $this->redirectToRoute('tag_list');
         }
 
         // Afficher le formulaire d'édition
         return $this->render('tag/edit.html.twig', [
             'form' => $form->createView(),
             'tag' => $tag, // Passer l'objet tag à la vue
         ]);
     }
}