<?php

namespace App\Controller;

use App\Entity\CategorieEquipement;
use App\Form\CategorieEquipementType;
use App\Repository\CategorieEquipementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieEquipementController extends AbstractController
{
    #[Route('/categorieEquipement', name: 'app_categorie_equipement')]
    public function index(): Response
    {
        return $this->render('categorie_equipement/index.html.twig', [
            'controller_name' => 'CategorieEquipementController',
        ]);
    }

    #[Route('/showCategorieEquipement', name: 'app_show_categorie_equipement')]
    public function showCategorieEquipement(CategorieEquipementRepository $categorieEquipementRepository): Response
    {
        $categories = $categorieEquipementRepository->findAll();

        return $this->render('categorie_equipement/showCategorieEquipement.html.twig', [
            'categoryList' => $categories,
        ]);
    }

    #[Route('/addCategorieEquipement', name: 'app_add_categorie_equipement')]
    public function addCategorieEquipement(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager();
        $categorieEquipement = new CategorieEquipement();
        $form = $this->createForm(CategorieEquipementType::class, $categorieEquipement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorieEquipement);
            $em->flush();

            $this->addFlash('add', 'Category added successfully!');
            return $this->redirectToRoute('app_show_categorie_equipement');
        }

        return $this->renderForm('categorie_equipement/addCategorieEquipement.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/editCategorieEquipement/{id}', name: 'app_edit_categorie_equipement')]
    public function editCategorieEquipement(
        int $id,
        Request $request,
        ManagerRegistry $doctrine,
        CategorieEquipementRepository $categorieEquipementRepository
    ): Response {
        $categorieEquipement = $categorieEquipementRepository->find($id);

        if (!$categorieEquipement) {
            throw $this->createNotFoundException('Category not found.');
        }

        $form = $this->createForm(CategorieEquipementType::class, $categorieEquipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($categorieEquipement);
            $entityManager->flush();

            $this->addFlash('edit', 'Category updated successfully!');
            return $this->redirectToRoute('app_show_categorie_equipement');
        }

        return $this->renderForm('categorie_equipement/editCategorieEquipement.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/deleteCategorieEquipement/{id}', name: 'app_delete_categorie_equipement')]
    public function deleteCategorieEquipement(
        int $id,
        ManagerRegistry $doctrine,
        CategorieEquipementRepository $categorieEquipementRepository
    ): Response {
        $categorieEquipement = $categorieEquipementRepository->find($id);

        if (!$categorieEquipement) {
            throw $this->createNotFoundException('Category not found.');
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($categorieEquipement);
        $entityManager->flush();

        $this->addFlash('delete', 'Category deleted successfully!');
        return $this->redirectToRoute('app_show_categorie_equipement');
    }
    #[Route('/statCategorieEquipement', name: 'app_stat_categorie_equipement')]
public function statCategorieEquipement(CategorieEquipementRepository $categorieEquipementRepository): Response
{
    $categoryStats = $categorieEquipementRepository->findCategoryStatistics();

    // Format data for Twig template
    $formattedStats = [];
    foreach ($categoryStats as $stat) {
        $formattedStats[$stat['categoryName']] = $stat['equipCount'];
    }

    return $this->render('categorie_equipement/statCategorieEquipement.html.twig', [
        'categoryStats' => $formattedStats,
    ]);
}


}
?>
