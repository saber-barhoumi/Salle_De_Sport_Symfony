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
use Symfony\Component\String\UnicodeString; // Ajouter cette ligne

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/new', name: 'produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handling the image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                // Utilisation de Symfony String pour la translittération
                $safeFilename = (new UnicodeString($originalFilename))
                    ->ascii() // Convertit les caractères non-ASCII en caractères ASCII
                    ->toString();

                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'), // Le répertoire où vous voulez enregistrer l'image
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception en cas d'erreur lors du téléchargement du fichier
                }

                // Définir le nom du fichier pour l'entité
                $produit->setImage($newFilename);
            }

            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/', name: 'produit_index', methods: ['GET'])]
 public function index(EntityManagerInterface $em): Response
 {
    // Récupérer tous les produits depuis la base de données
    $produits = $em->getRepository(Produit::class)->findAll();

    // Renvoyer la vue avec les produits récupérés
    return $this->render('produit/index.html.twig', [
        'produits' => $produits,
    ]);
 }
 #[Route('/{id}', name: 'produit_show', methods: ['GET'])]
 public function show(int $id, EntityManagerInterface $em): Response
 {
     $produit = $em->getRepository(Produit::class)->find($id);

     if (!$produit) {
         throw $this->createNotFoundException('No product found for id ' . $id);
     }

     return $this->render('produit/show.html.twig', [
         'produit' => $produit,
     ]);
 }
 #[Route('/{id}/edit', name: 'produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // Save the updated product

            return $this->redirectToRoute('produit_index'); // Redirect to product list
        }

        return $this->render('produit/edit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);

    }
    
    #[Route('/{id}/delete', name: 'produit_delete', methods: ['POST'])]
public function delete(Request $request, Produit $produit, EntityManagerInterface $em): Response
{
    // Ensure the form was submitted from the correct page
    if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
        $em->remove($produit);
        $em->flush();
    }

    return $this->redirectToRoute('produit_index'); // Redirect to the product index page after deletion
}
    



}