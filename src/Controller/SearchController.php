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
        $depart = $request->query->get('villeDepart');
        $destination = $request->query->get('villeArrivee');
        $date = $request->query->get('dateDepart');

        $trajets = [];

        if ($depart && $destination && $date) {
            // Convertir la date string en DateTime
            $dateObj = new \DateTime($date);
            
            // Rechercher les horaires disponibles
            $trajets = $horaireRepository->findAvailableHoraires(
                $depart,
                $destination,
                $dateObj
            );
        }

        return $this->render('search/results.html.twig', [
            'trajets' => $trajets,
            'depart' => $depart,
            'destination' => $destination,
            'date' => $date,
        ]);
    }
}