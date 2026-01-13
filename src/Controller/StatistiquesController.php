<?php

namespace App\Controller;

use App\Repository\BusRepository;
use App\Repository\ChauffeurRepository;
use App\Repository\HoraireRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/statistiques')]
#[IsGranted('ROLE_ADMIN')]
class StatistiquesController extends AbstractController
{
    #[Route('/', name: 'app_admin_statistiques')]
    public function index(
        BusRepository $busRepository,
        ChauffeurRepository $chauffeurRepository,
        HoraireRepository $horaireRepository,
        ReservationRepository $reservationRepository,
        UserRepository $userRepository
    ): Response {
        // Statistiques générales
        $stats = [
            'bus' => [
                'total' => $busRepository->count([]),
                'disponibles' => 0, // On enlève le filtre par statut
                'maintenance' => 0,  // On enlève le filtre par statut
            ],
            'chauffeurs' => [
                'total' => $chauffeurRepository->count([]),
                'actifs' => $chauffeurRepository->count(['statut' => 'actif']),
                'inactifs' => $chauffeurRepository->count(['statut' => 'inactif']),
                'en_conge' => $chauffeurRepository->count(['statut' => 'en_conge']),
            ],
            'horaires' => [
                'total' => $horaireRepository->count([]),
                'actifs' => $horaireRepository->count(['statut' => 'actif']),
                'inactifs' => $horaireRepository->count(['statut' => 'inactif']),
            ],
            'reservations' => [
                'total' => $reservationRepository->count([]),
                'confirmees' => $reservationRepository->count(['statut' => 'confirmee']),
                'annulees' => $reservationRepository->count(['statut' => 'annulee']),
            ],
            'utilisateurs' => [
                'total' => $userRepository->count([]),
            ],
        ];

        // Calculer le revenu total
        $reservations = $reservationRepository->findBy(['statut' => 'confirmee']);
        $revenuTotal = array_sum(array_map(fn($r) => (float)$r->getMontant(), $reservations));

        // Récupérer les dernières réservations
        $dernieresReservations = $reservationRepository->findBy([], ['dateReservation' => 'DESC'], 5);

        // Calculer les statistiques par mois (6 derniers mois)
        $reservationsParMois = $this->getReservationsParMois($reservationRepository);

        return $this->render('statistiques/index.html.twig', [
            'stats' => $stats,
            'revenu_total' => $revenuTotal,
            'dernieres_reservations' => $dernieresReservations,
            'reservations_par_mois' => $reservationsParMois,
        ]);
    }

    private function getReservationsParMois(ReservationRepository $reservationRepository): array
    {
        $data = [];
        $currentDate = new \DateTime();
        
        for ($i = 5; $i >= 0; $i--) {
            $date = clone $currentDate;
            $date->modify("-$i month");
            $startDate = $date->modify('first day of this month')->setTime(0, 0, 0);
            $endDate = clone $startDate;
            $endDate->modify('last day of this month')->setTime(23, 59, 59);
            
            $count = count($reservationRepository->createQueryBuilder('r')
                ->where('r.dateReservation BETWEEN :start AND :end')
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getResult());
            
            $data[] = [
                'mois' => $startDate->format('M Y'),
                'count' => $count
            ];
        }
        
        return $data;
    }
}