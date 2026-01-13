<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/reservations')]
#[IsGranted('ROLE_ADMIN')]
class AdminReservationController extends AbstractController
{
    #[Route('/', name: 'app_admin_reservations')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findBy([], ['dateReservation' => 'DESC']);

        $stats = [
            'total' => count($reservations),
            'confirmees' => count(array_filter($reservations, fn($r) => $r->getStatut() === 'confirmee')),
            'annulees' => count(array_filter($reservations, fn($r) => $r->getStatut() === 'annulee')),
        ];

        return $this->render('reservation/admin_index.html.twig', [
            'reservations' => $reservations,
            'stats' => $stats,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_reservation_show')]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/admin_show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_admin_reservation_cancel', methods: ['POST'])]
    public function cancel(
        Reservation $reservation,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if ($this->isCsrfTokenValid('cancel'.$reservation->getId(), $request->request->get('_token'))) {
            $horaire = $reservation->getHoraire();
            $horaire->setPlacesDisponibles($horaire->getPlacesDisponibles() + $reservation->getNombrePlaces());

            $reservation->setStatut('annulee');
            $entityManager->flush();

            $this->addFlash('success', 'Réservation annulée avec succès');
        }

        return $this->redirectToRoute('app_admin_reservations');
    }
}