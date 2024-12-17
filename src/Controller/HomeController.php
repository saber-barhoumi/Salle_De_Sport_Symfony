<?php

namespace App\Controller;

use App\Repository\EquipementRepository;
use App\Repository\CategorieEquipementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(
        EquipementRepository $equipementRepository,
        CategorieEquipementRepository $categorieEquipementRepository
    ): Response {
        // Fetch the equipment and category data
        $equipementList = $equipementRepository->findAll();
        $categoryList = $categorieEquipementRepository->findAll();

        return $this->render('base2.html.twig', [
            'equipementList' => $equipementList,
            'categoryList' => $categoryList
        ]);
    }
}
?>