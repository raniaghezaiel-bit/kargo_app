<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();

        // Rediriger selon le rôle
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('dashboard/admin.html.twig');
        }

        // Dashboard passager avec ses dernières réservations
        $reservations = $reservationRepository->findBy(
            ['passager' => $user],
            ['dateReservation' => 'DESC'],
            5  // Limiter à 5 dernières réservations
        );

        return $this->render('dashboard/passager.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDashboard(): Response
    {
        return $this->render('dashboard/admin.html.twig');
    }
}