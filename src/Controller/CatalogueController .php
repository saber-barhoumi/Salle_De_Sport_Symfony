<?php
namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue/pdf', name: 'generate_catalogue_pdf', methods: ['GET'])]
    public function generatePDF(): Response
    {
        // Récupérer tous les produits de la base de données
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findAll();

        // Générer le PDF
        $pdf = new TCPDF();
        $pdf->AddPage();

        // Titre du catalogue
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Catalogue de Produits', 0, 1, 'C');

        // Parcourir tous les produits
        $pdf->SetFont('Helvetica', '', 12);
        foreach ($produits as $produit) {
            // Nom du produit
            $pdf->Cell(0, 10, 'Nom du produit : ' . $produit->getNom(), 0, 1);

            // Prix du produit
            $pdf->Cell(0, 10, 'Prix : ' . $produit->getPrix() . '€', 0, 1);

            // Description du produit
            $pdf->Cell(0, 10, 'Description : ' . $produit->getDescription(), 0, 1);
            
            // Ajouter l'image du produit
            // Assurez-vous que le chemin de l'image est correct
            $imagePath = $produit->getImagePath(); // Vous pouvez ajuster cette méthode pour obtenir le chemin de l'image
            if (file_exists($imagePath)) {
                $pdf->Image($imagePath, 10, $pdf->GetY(), 30, 30, '', '', '', false, 300, '', false, false, 0, 'C');
                $pdf->Ln(35); // Pour espacer l'image du texte suivant
            }

            // Espacement entre les produits
            $pdf->Ln(10);
        }

        // Envoi du fichier PDF en téléchargement
        $pdf->Output('catalogue.pdf', 'D');  // 'D' pour télécharger
        return new Response('PDF généré avec succès');
    }
}
