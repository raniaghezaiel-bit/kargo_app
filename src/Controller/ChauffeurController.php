<?php

namespace App\Controller;

use App\Entity\Chauffeur;
use App\Form\ChauffeurType;
use App\Repository\ChauffeurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/chauffeurs')]
#[IsGranted('ROLE_ADMIN')]
class ChauffeurController extends AbstractController
{
    #[Route('/', name: 'admin_chauffeur_index', methods: ['GET'])]
    public function index(Request $request, ChauffeurRepository $chauffeurRepository): Response
    {
        // Récupération des paramètres de recherche et filtres
        $search = $request->query->get('search', '');
        $statutFilter = $request->query->get('statut', '');
        $villeFilter = $request->query->get('ville', '');
        
        // Récupération des chauffeurs avec filtres
        $queryBuilder = $chauffeurRepository->createQueryBuilder('c');
        
        // Filtre de recherche (nom, prénom, CIN)
        if (!empty($search)) {
            $queryBuilder->andWhere('c.nom LIKE :search OR c.prenom LIKE :search OR c.cin LIKE :search')
                        ->setParameter('search', '%' . $search . '%');
        }
        
        // Filtre par statut
        if (!empty($statutFilter)) {
            $queryBuilder->andWhere('c.statut = :statut')
                        ->setParameter('statut', $statutFilter);
        }
        
        // Filtre par ville
        if (!empty($villeFilter)) {
            $queryBuilder->andWhere('c.ville = :ville')
                        ->setParameter('ville', $villeFilter);
        }
        
        $queryBuilder->orderBy('c.id', 'DESC');
        $chauffeurs = $queryBuilder->getQuery()->getResult();
        
        // Calcul des statistiques (sur tous les chauffeurs, sans filtres)
        $allChauffeurs = $chauffeurRepository->findAll();
        
        $stats = [
            'total' => count($allChauffeurs),
            'actifs' => count(array_filter($allChauffeurs, fn($c) => $c->getStatut() === 'actif')),
            'inactifs' => count(array_filter($allChauffeurs, fn($c) => $c->getStatut() === 'inactif')),
            'en_conge' => count(array_filter($allChauffeurs, fn($c) => $c->getStatut() === 'en_conge')),
            'suspendus' => count(array_filter($allChauffeurs, fn($c) => $c->getStatut() === 'suspendu')),
        ];
        
        return $this->render('chauffeur/index.html.twig', [
            'chauffeurs' => $chauffeurs,
            'stats' => $stats,
            'search' => $search,
            'statut' => $statutFilter,
            'ville' => $villeFilter,
        ]);
    }

    #[Route('/nouveau', name: 'admin_chauffeur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chauffeur = new Chauffeur();
        $form = $this->createForm(ChauffeurType::class, $chauffeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chauffeur);
            $entityManager->flush();

            $this->addFlash('success', 'Chauffeur ajouté avec succès !');

            return $this->redirectToRoute('admin_chauffeur_index');
        }

        return $this->render('chauffeur/new.html.twig', [
            'chauffeur' => $chauffeur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_chauffeur_show', methods: ['GET'])]
    public function show(Chauffeur $chauffeur): Response
    {
        return $this->render('chauffeur/show.html.twig', [
            'chauffeur' => $chauffeur,
        ]);
    }

    #[Route('/{id}/modifier', name: 'admin_chauffeur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chauffeur $chauffeur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChauffeurType::class, $chauffeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Chauffeur modifié avec succès !');

            return $this->redirectToRoute('admin_chauffeur_index');
        }

        return $this->render('chauffeur/edit.html.twig', [
            'chauffeur' => $chauffeur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'admin_chauffeur_delete', methods: ['POST'])]
    public function delete(Request $request, Chauffeur $chauffeur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chauffeur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chauffeur);
            $entityManager->flush();

            $this->addFlash('success', 'Chauffeur supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_chauffeur_index');
    }

    #[Route('/{id}/changer-statut/{statut}', name: 'admin_chauffeur_change_status', methods: ['POST'])]
    public function changeStatus(
        Request $request,
        Chauffeur $chauffeur,
        string $statut,
        EntityManagerInterface $entityManager
    ): Response {
        // Validation du statut
        $statutsValides = ['actif', 'inactif', 'en_conge', 'suspendu'];
        
        if (!in_array($statut, $statutsValides)) {
            $this->addFlash('error', 'Statut invalide !');
            return $this->redirectToRoute('admin_chauffeur_index');
        }
        
        if ($this->isCsrfTokenValid('status'.$chauffeur->getId(), $request->request->get('_token'))) {
            $chauffeur->setStatut($statut);
            $entityManager->flush();

            $this->addFlash('success', 'Statut du chauffeur modifié avec succès !');
        }

        return $this->redirectToRoute('admin_chauffeur_index');
    }
}