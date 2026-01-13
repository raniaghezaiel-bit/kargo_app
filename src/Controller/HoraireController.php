<?php

namespace App\Controller;

use App\Entity\Horaire;
use App\Form\HoraireType;
use App\Repository\HoraireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/horaires')]
#[IsGranted('ROLE_ADMIN')]
class HoraireController extends AbstractController
{
    #[Route('/', name: 'admin_horaire_index', methods: ['GET'])]
    public function index(HoraireRepository $horaireRepository, Request $request): Response
    {
        $villeDepart = $request->query->get('ville_depart');
        $villeArrivee = $request->query->get('ville_arrivee');
        $statut = $request->query->get('statut');
        
        $horaires = $horaireRepository->findAll();
        
        if ($villeDepart || $villeArrivee || $statut) {
            $horaires = array_filter($horaires, function($h) use ($villeDepart, $villeArrivee, $statut) {
                $match = true;
                
                if ($villeDepart && $h->getTrajet()->getVilleDepart() !== $villeDepart) {
                    $match = false;
                }
                
                if ($villeArrivee && $h->getTrajet()->getVilleArrivee() !== $villeArrivee) {
                    $match = false;
                }
                
                if ($statut && $h->getStatut() !== $statut) {
                    $match = false;
                }
                
                return $match;
            });
        }
        
        $stats = [
            'total' => count($horaires),
            'actifs' => count(array_filter($horaires, fn($h) => $h->getStatut() === 'actif')),
            'inactifs' => count(array_filter($horaires, fn($h) => $h->getStatut() === 'inactif')),
        ];
        
        return $this->render('horaire/index.html.twig', [
            'horaires' => $horaires,
            'stats' => $stats,
            'ville_depart' => $villeDepart,
            'ville_arrivee' => $villeArrivee,
            'statut' => $statut,
        ]);
    }

    #[Route('/nouveau', name: 'admin_horaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $horaire = new Horaire();
        $form = $this->createForm(HoraireType::class, $horaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($horaire->getBus()) {
                $horaire->setPlacesDisponibles($horaire->getBus()->getCapacite());
            }

            $entityManager->persist($horaire);
            $entityManager->flush();

            $this->addFlash('success', 'Horaire ajouté avec succès !');

            return $this->redirectToRoute('admin_horaire_index');
        }

        return $this->render('horaire/new.html.twig', [
            'horaire' => $horaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_horaire_show', methods: ['GET'])]
    public function show(Horaire $horaire): Response
    {
        return $this->render('horaire/show.html.twig', [
            'horaire' => $horaire,
        ]);
    }

    #[Route('/{id}/modifier', name: 'admin_horaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Horaire $horaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HoraireType::class, $horaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Horaire modifié avec succès !');

            return $this->redirectToRoute('admin_horaire_index');
        }

        return $this->render('horaire/edit.html.twig', [
            'horaire' => $horaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'admin_horaire_delete', methods: ['POST'])]
    public function delete(Request $request, Horaire $horaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$horaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($horaire);
            $entityManager->flush();

            $this->addFlash('success', 'Horaire supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_horaire_index');
    }

    #[Route('/{id}/changer-statut/{statut}', name: 'admin_horaire_change_status', methods: ['POST'])]
    public function changeStatus(
        Request $request,
        Horaire $horaire,
        string $statut,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('status'.$horaire->getId(), $request->request->get('_token'))) {
            $horaire->setStatut($statut);
            $entityManager->flush();

            $this->addFlash('success', 'Statut de l\'horaire modifié avec succès !');
        }

        return $this->redirectToRoute('admin_horaire_index');
    }
}