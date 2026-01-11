<?php

namespace App\Controller;

use App\Entity\Chauffeur;
use App\Form\ChauffeurType;
use App\Repository\ChauffeurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/chauffeurs')]
#[IsGranted('ROLE_ADMIN')]
class ChauffeurController extends AbstractController
{
    #[Route('/', name: 'admin_chauffeur_index', methods: ['GET'])]
    public function index(ChauffeurRepository $chauffeurRepository, Request $request): Response
    {
        // Récupération des paramètres de recherche et filtres
        $search = $request->query->get('search', '');
        $statut = $request->query->get('statut', '');
        $ville = $request->query->get('ville', '');

        // Création du QueryBuilder
        $qb = $chauffeurRepository->createQueryBuilder('c');

        // Filtre de recherche
        if ($search) {
            $qb->andWhere('c.nom LIKE :search OR c.prenom LIKE :search OR c.cin LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Filtre par statut
        if ($statut) {
            $qb->andWhere('c.statut = :statut')
               ->setParameter('statut', $statut);
        }

        // Filtre par ville
        if ($ville) {
            $qb->andWhere('c.ville = :ville')
               ->setParameter('ville', $ville);
        }

        // Tri par date de création (plus récent en premier)
        $qb->orderBy('c.dateCreation', 'DESC');

        $chauffeurs = $qb->getQuery()->getResult();

        // Statistiques
        $stats = [
            'total' => $chauffeurRepository->count([]),
            'actifs' => $chauffeurRepository->count(['statut' => 'actif']),
            'inactifs' => $chauffeurRepository->count(['statut' => 'inactif']),
            'en_conge' => $chauffeurRepository->count(['statut' => 'en_conge']),
            'suspendus' => $chauffeurRepository->count(['statut' => 'suspendu']),
        ];

        return $this->render('chauffeur/index.html.twig', [
            'chauffeurs' => $chauffeurs,
            'stats' => $stats,
            'search' => $search,
            'statut' => $statut,
            'ville' => $ville,
        ]);
    }

    #[Route('/nouveau', name: 'admin_chauffeur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $chauffeur = new Chauffeur();
        $form = $this->createForm(ChauffeurType::class, $chauffeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de la photo
            $photoFile = $form->get('photoFile')->getData();
            
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('chauffeurs_photos_directory'),
                        $newFilename
                    );
                    $chauffeur->setPhoto($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo : ' . $e->getMessage());
                }
            }
            
            $chauffeur->setDateCreation(new \DateTime());
            
            $entityManager->persist($chauffeur);
            $entityManager->flush();

            $this->addFlash('success', 'Le chauffeur ' . $chauffeur->getNomComplet() . ' a été ajouté avec succès !');

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
    public function edit(Request $request, Chauffeur $chauffeur, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ChauffeurType::class, $chauffeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de la photo
            $photoFile = $form->get('photoFile')->getData();
            
            if ($photoFile) {
                // Supprimer l'ancienne photo si elle existe
                if ($chauffeur->getPhoto()) {
                    $oldPhotoPath = $this->getParameter('chauffeurs_photos_directory') . '/' . $chauffeur->getPhoto();
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('chauffeurs_photos_directory'),
                        $newFilename
                    );
                    $chauffeur->setPhoto($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo : ' . $e->getMessage());
                }
            }
            
            $chauffeur->setDateModification(new \DateTime());
            
            $entityManager->flush();

            $this->addFlash('success', 'Le chauffeur ' . $chauffeur->getNomComplet() . ' a été modifié avec succès !');

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
        if ($this->isCsrfTokenValid('delete' . $chauffeur->getId(), $request->request->get('_token'))) {
            // Supprimer la photo si elle existe
            if ($chauffeur->getPhoto()) {
                $photoPath = $this->getParameter('chauffeurs_photos_directory') . '/' . $chauffeur->getPhoto();
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }
            
            $nom = $chauffeur->getNomComplet();
            $entityManager->remove($chauffeur);
            $entityManager->flush();

            $this->addFlash('success', 'Le chauffeur ' . $nom . ' a été supprimé avec succès.');
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
        if ($this->isCsrfTokenValid('status' . $chauffeur->getId(), $request->request->get('_token'))) {
            $chauffeur->setStatut($statut);
            $chauffeur->setDateModification(new \DateTime());
            
            $entityManager->flush();

            $this->addFlash('success', 'Le statut du chauffeur a été changé avec succès.');
        }

        return $this->redirectToRoute('admin_chauffeur_show', ['id' => $chauffeur->getId()]);
    }
}