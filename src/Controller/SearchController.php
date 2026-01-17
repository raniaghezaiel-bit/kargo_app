<?php

namespace App\Controller;

use App\Repository\HoraireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search/trajet', name: 'app_search_trajet', methods: ['GET'])]
    public function searchTrajet(Request $request, HoraireRepository $horaireRepository): Response
    {
        // Récupérer les paramètres de recherche
        $villeDepart = $request->query->get('villeDepart');
        $villeArrivee = $request->query->get('villeArrivee');
        $dateDepart = $request->query->get('dateDepart');

        $trajets = [];

        // Si tous les critères sont remplis, rechercher
        if ($villeDepart && $villeArrivee) {
            // Rechercher les horaires correspondants
            $trajets = $horaireRepository->createQueryBuilder('h')
                ->where('h.villeDepart = :depart')
                ->andWhere('h.villeArrivee = :arrivee')
                ->andWhere('h.statut = :statut')
                ->setParameter('depart', $villeDepart)
                ->setParameter('arrivee', $villeArrivee)
                ->setParameter('statut', 'actif')
                ->orderBy('h.heureDepart', 'ASC')
                ->getQuery()
                ->getResult();
        }

        return $this->render('search/results.html.twig', [
            'trajets' => $trajets,
            'depart' => $villeDepart,
            'destination' => $villeArrivee,
            'date' => $dateDepart,
        ]);
    }
}