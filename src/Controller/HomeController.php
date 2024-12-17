<?php

namespace App\Controller;

use App\Repository\EquipementRepository;
use App\Repository\CategorieEquipementRepository;
use App\Repository\TypeSeanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(
        EquipementRepository $equipementRepository,
        CategorieEquipementRepository $categorieEquipementRepository,
        EntityManagerInterface $entityManager // Added for TypeSeance
    ): Response {
        // Fetch the equipment and category data
        $equipementList = $equipementRepository->findAll();
        $categoryList = $categorieEquipementRepository->findAll();

        // Fetch TypeSeance data using DQL
        $dql = "SELECT t.type, t.description FROM App\Entity\TypeSeance t";
        $query = $entityManager->createQuery($dql);
        $typeSeances = $query->getResult();

        // Render the view with the data passed to Twig
        return $this->render('base2.html.twig', [
            'equipementList' => $equipementList,
            'categoryList' => $categoryList,
            'typeSeances' => $typeSeances // Passing TypeSeance data
        ]);
    }
}

