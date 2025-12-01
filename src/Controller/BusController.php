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

#[Route('/bus')]  // ← CHANGÉ : retiré /admin
#[IsGranted('ROLE_ADMIN')]
class BusController extends AbstractController
{
    #[Route('/', name: 'app_bus_index', methods: ['GET'])]  // ← CHANGÉ : retiré _admin_
    public function index(BusRepository $busRepository): Response
    {
        return $this->render('bus/index.html.twig', [
            'buses' => $busRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_bus_new', methods: ['GET', 'POST'])]  // ← CHANGÉ
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bus = new Bus();
        $form = $this->createForm(BusType::class, $bus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bus->setAdmin($this->getUser());
            
            $entityManager->persist($bus);
            $entityManager->flush();

            $this->addFlash('success', 'Le bus a été ajouté avec succès !');

            return $this->redirectToRoute('app_bus_index');  // ← CHANGÉ
        }

        return $this->render('bus/new.html.twig', [
            'bus' => $bus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bus_show', methods: ['GET'])]  // ← CHANGÉ
    public function show(Bus $bus): Response
    {
        return $this->render('bus/show.html.twig', [
            'bus' => $bus,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_bus_edit', methods: ['GET', 'POST'])]  // ← CHANGÉ
    public function edit(Request $request, Bus $bus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BusType::class, $bus, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le bus a été modifié avec succès !');

            return $this->redirectToRoute('app_bus_index');  // ← CHANGÉ
        }

        return $this->render('bus/edit.html.twig', [
            'bus' => $bus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bus_delete', methods: ['POST'])]  // ← CHANGÉ
    public function delete(Request $request, Bus $bus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bus->getId(), $request->request->get('_token'))) {
            if ($bus->getTrajets()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer ce bus car il est assigné à des trajets.');
            } else {
                $entityManager->remove($bus);
                $entityManager->flush();
                $this->addFlash('success', 'Le bus a été supprimé avec succès !');
            }
        }

        return $this->redirectToRoute('app_bus_index');  // ← CHANGÉ
    }
}