<?php

namespace App\Controller;
use App\Entity\TypeSeance;
use App\Form\TypeSeanceformType;
use App\Repository\TypeSeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SeanceTypeController extends AbstractController
{
    #[Route('/typeseance', name: 'app_typeseance')]
    public function index(): Response
    {
        return $this->render('typeseance/index.html.twig', [
            'controller_name' => 'SeanceTypeController',
        ]);
    }

    #[Route('/typeseance/list', name: 'list_typeseance')]
    public function listTypeseance(TypeSeanceRepository $seanceTypeRepository): Response
    {
        $types = $seanceTypeRepository->findAll();

        return $this->render('typeseance/list.html.twig', [
            'types' => $types,
        ]);
    }

    #[Route('/typeseance/new', name: 'add_typeseance')]
    public function addTypeseance(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeSeance = new TypeSeance();
        $form = $this->createForm(TypeSeanceformType::class, $typeSeance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeSeance);
            $entityManager->flush();

            return $this->redirectToRoute('list_typeseance');
        }

        return $this->render('typeseance/nouveau.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/typeseance/delete/{id}', name: 'delete_typeseance')]
    public function deleteTypeseance(int $id, EntityManagerInterface $entityManager): Response
    {
        $typeSeance = $entityManager->getRepository(TypeSeance::class)->find($id);

        if ($typeSeance) {
            $entityManager->remove($typeSeance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('list_typeseance');
    }

    #[Route('/typeseance/modify/{id}', name: 'modify_typeseance')]
    public function modifyTypeseance(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeSeance = $entityManager->getRepository(TypeSeance::class)->find($id);

        if (!$typeSeance) {
            throw $this->createNotFoundException('Type de séance non trouvé');
        }

        $form = $this->createForm(TypeSeanceformType::class, $typeSeance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeSeance);
            $entityManager->flush();

            return $this->redirectToRoute('list_typeseance');
        }

        return $this->render('typeseance/modify.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
