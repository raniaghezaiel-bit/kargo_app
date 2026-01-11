<?php

namespace App\Controller;

use App\Repository\TrajetRepository;
use App\Repository\HoraireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search/trajet', name: 'app_search_trajet')]
    public function searchTrajet(
        Request $request,
        TrajetRepository $trajetRepository,
        HoraireRepository $horaireRepository
    ): Response {
        $depart = $request->query->get('depart');
        $destination = $request->query->get('arrivee');
        $date = $request->query->get('date');

        $trajets = $trajetRepository->findBySearch($depart, $destination);

        return $this->render('search/results.html.twig', [
            'trajets' => $trajets,
            'depart' => $depart,
            'destination' => $destination,
            'date' => $date,
        ]);
    }
}