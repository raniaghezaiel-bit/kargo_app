<?php

namespace App\Controller;

use App\Entity\Bus;
use App\Form\BusType;
use App\Repository\BusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/bus')]
#[IsGranted('ROLE_ADMIN')]
class BusController extends AbstractController
{
    #[Route('/', name: 'app_bus_index', methods: ['GET'])]
    public function index(BusRepository $busRepository): Response
    {
        $buses = $busRepository->findAll();
        
        $stats = [
            'total' => count($buses),
            'disponibles' => 0,
            'maintenance' => 0,
        ];
        
        return $this->render('bus/index.html.twig', [
            'buses' => $buses,
            'stats' => $stats,
        ]);
    }

    #[Route('/new', name: 'app_bus_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bus = new Bus();
        $form = $this->createForm(BusType::class, $bus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bus);
            $entityManager->flush();

            $this->addFlash('success', 'Bus ajouté avec succès !');

            return $this->redirectToRoute('app_bus_index');
        }

        return $this->render('bus/new.html.twig', [
            'bus' => $bus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bus_show', methods: ['GET'])]
    public function show(Bus $bus): Response
    {
        return $this->render('bus/show.html.twig', [
            'bus' => $bus,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_bus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bus $bus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BusType::class, $bus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Bus modifié avec succès !');

            return $this->redirectToRoute('app_bus_index');
        }

        return $this->render('bus/edit.html.twig', [
            'bus' => $bus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bus_delete', methods: ['POST'])]
    public function delete(Request $request, Bus $bus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bus->getId(), $request->request->get('_token'))) {
            $entityManager->remove($bus);
            $entityManager->flush();

            $this->addFlash('success', 'Bus supprimé avec succès !');
        }

        return $this->redirectToRoute('app_bus_index');
    }
}