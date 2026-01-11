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
        // Récupération des paramètres de recherche et filtres
        $villeDepart = $request->query->get('ville_depart', '');
        $villeArrivee = $request->query->get('ville_arrivee', '');
        $statut = $request->query->get('statut', '');

        // Création du QueryBuilder
        $qb = $horaireRepository->createQueryBuilder('h');

        // Filtre par ville de départ
        if ($villeDepart) {
            $qb->andWhere('h.villeDepart = :depart')
               ->setParameter('depart', $villeDepart);
        }

        // Filtre par ville d'arrivée
        if ($villeArrivee) {
            $qb->andWhere('h.villeArrivee = :arrivee')
               ->setParameter('arrivee', $villeArrivee);
        }

        // Filtre par statut
        if ($statut) {
            $qb->andWhere('h.statut = :statut')
               ->setParameter('statut', $statut);
        }

        // Tri par ville de départ puis heure de départ
        $qb->orderBy('h.villeDepart', 'ASC')
           ->addOrderBy('h.heureDepart', 'ASC');

        $horaires = $qb->getQuery()->getResult();

        // Statistiques
        $stats = [
            'total' => $horaireRepository->count([]),
            'actifs' => $horaireRepository->count(['statut' => 'actif']),
            'inactifs' => $horaireRepository->count(['statut' => 'inactif']),
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
            // Validation : ville de départ != ville d'arrivée
            if ($horaire->getVilleDepart() === $horaire->getVilleArrivee()) {
                $this->addFlash('error', 'La ville de départ et d\'arrivée doivent être différentes.');
                return $this->render('horaire/new.html.twig', [
                    'horaire' => $horaire,
                    'form' => $form,
                ]);
            }

            // Validation : heure d'arrivée > heure de départ
            if ($horaire->getHeureArrivee() <= $horaire->getHeureDepart()) {
                $this->addFlash('error', 'L\'heure d\'arrivée doit être postérieure à l\'heure de départ.');
                return $this->render('horaire/new.html.twig', [
                    'horaire' => $horaire,
                    'form' => $form,
                ]);
            }

            $horaire->setDateCreation(new \DateTime());
            
            $entityManager->persist($horaire);
            $entityManager->flush();

            $this->addFlash('success', 'L\'horaire ' . $horaire->getTrajet() . ' a été ajouté avec succès !');

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
            // Validation : ville de départ != ville d'arrivée
            if ($horaire->getVilleDepart() === $horaire->getVilleArrivee()) {
                $this->addFlash('error', 'La ville de départ et d\'arrivée doivent être différentes.');
                return $this->render('horaire/edit.html.twig', [
                    'horaire' => $horaire,
                    'form' => $form,
                ]);
            }

            // Validation : heure d'arrivée > heure de départ
            if ($horaire->getHeureArrivee() <= $horaire->getHeureDepart()) {
                $this->addFlash('error', 'L\'heure d\'arrivée doit être postérieure à l\'heure de départ.');
                return $this->render('horaire/edit.html.twig', [
                    'horaire' => $horaire,
                    'form' => $form,
                ]);
            }

            $horaire->setDateModification(new \DateTime());
            
            $entityManager->flush();

            $this->addFlash('success', 'L\'horaire ' . $horaire->getTrajet() . ' a été modifié avec succès !');

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
        if ($this->isCsrfTokenValid('delete' . $horaire->getId(), $request->request->get('_token'))) {
            $trajet = $horaire->getTrajet();
            $entityManager->remove($horaire);
            $entityManager->flush();

            $this->addFlash('success', 'L\'horaire ' . $trajet . ' a été supprimé avec succès.');
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
        if ($this->isCsrfTokenValid('status' . $horaire->getId(), $request->request->get('_token'))) {
            $horaire->setStatut($statut);
            $horaire->setDateModification(new \DateTime());
            
            $entityManager->flush();

            $this->addFlash('success', 'Le statut de l\'horaire a été changé avec succès.');
        }

        return $this->redirectToRoute('admin_horaire_show', ['id' => $horaire->getId()]);
    }
}