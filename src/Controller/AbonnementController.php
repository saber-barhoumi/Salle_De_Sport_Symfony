<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Abonnementachat;
use DateTimeImmutable;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/abonnement')]
final class AbonnementController extends AbstractController
{
    #[Route(name: 'app_abonnement_index', methods: ['GET'])]
    public function index(AbonnementRepository $abonnementRepository): Response
    {
        return $this->render('abonnement/index.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_abonnement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $abonnement = new Abonnement();
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($abonnement);
            $entityManager->flush();

            $this->addFlash('success', 'Abonnement créé avec succès.');

            return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('abonnement/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_abonnement_show', methods: ['GET'])]
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('abonnement/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_abonnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Abonnement mis à jour avec succès.');

            return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_abonnement_delete', methods: ['POST'])]
    public function delete(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $abonnement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($abonnement);
            $entityManager->flush();

            $this->addFlash('success', 'Abonnement supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Échec de la suppression : token CSRF invalide.');
        }

        return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/acheter', name: 'acheter_abonnement', methods: ['POST'])]
public function acheter(Abonnement $abonnement, EntityManagerInterface $entityManager): Response
{
    if ($abonnement->getCapacite() > 0) {
        // Décrémenter la capacité
        $abonnement->setCapacite($abonnement->getCapacite() - 1);

        // Créer une nouvelle entité Abonnementachat
        $achat = new Abonnementachat();
        $achat->setAbonnement($abonnement);
        $achat->setDateAchat(new DateTimeImmutable());

        // Enregistrer les deux entités
        $entityManager->persist($achat);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Achat effectué avec succès !',
            'new_capacite' => $abonnement->getCapacite(),
        ]);
    }

    return $this->json([
        'status' => 'error',
        'message' => 'Abonnement complet.',
    ]);
}
#[Route('/acheter/client', name: 'achat_abonnement')]
    public function abonnementAchatDashboard(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);

        $query = $entityManager->getRepository(Abonnementachat::class)->createQueryBuilder('a')
            ->orderBy('a.dateAchat', 'DESC')
            ->getQuery();

        $abonnementAchats = $paginator->paginate(
            $query, // 
            $page,
            5 
        );

        return $this->render('abonnement/achatabonnement.html.twig', [
            'abonnementAchats' => $abonnementAchats,
        ]);
    }



}
