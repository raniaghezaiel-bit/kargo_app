<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\HoraireRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservations')]
#[IsGranted('ROLE_PASSAGER')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservations')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        $passager = $this->getUser();
        $reservations = $reservationRepository->findBy(['passager' => $passager], ['dateReservation' => 'DESC']);

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/new/{horaireId}', name: 'app_reservation_new')]
    public function new(
        int $horaireId,
        HoraireRepository $horaireRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $horaire = $horaireRepository->find($horaireId);

        if (!$horaire) {
            throw $this->createNotFoundException('Horaire non trouvé');
        }

        if ($request->isMethod('POST')) {
            $nombrePlaces = (int) $request->request->get('nombrePlaces', 1);

            // Vérifier disponibilité
            if ($horaire->getPlacesDisponibles() < $nombrePlaces) {
                $this->addFlash('error', 'Pas assez de places disponibles');
                return $this->redirectToRoute('app_reservation_new', ['horaireId' => $horaireId]);
            }

            // Créer la réservation
            $reservation = new Reservation();
            $reservation->setPassager($this->getUser());
            $reservation->setHoraire($horaire);
            $reservation->setNombrePlaces($nombrePlaces);
            $reservation->setMontant((string)($horaire->getTrajet()->getPrix() * $nombrePlaces));
            $reservation->setStatut('confirmee');

            // Générer QR Code (simple pour l'instant)
            $reservation->setQrCode($reservation->getNumeroReservation());

            // Mettre à jour places disponibles
            $horaire->setPlacesDisponibles($horaire->getPlacesDisponibles() - $nombrePlaces);

            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation confirmée ! Numéro : ' . $reservation->getNumeroReservation());

            return $this->redirectToRoute('app_reservation_show', ['id' => $reservation->getId()]);
        }

        return $this->render('reservation/new.html.twig', [
            'horaire' => $horaire,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show')]
    public function show(Reservation $reservation): Response
    {
        // Vérifier que c'est bien la réservation du passager connecté
        if ($reservation->getPassager() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_reservation_cancel', methods: ['POST'])]
    public function cancel(
        Reservation $reservation,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if ($reservation->getPassager() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('cancel'.$reservation->getId(), $request->request->get('_token'))) {
            // Libérer les places
            $horaire = $reservation->getHoraire();
            $horaire->setPlacesDisponibles($horaire->getPlacesDisponibles() + $reservation->getNombrePlaces());

            $reservation->setStatut('annulee');
            $entityManager->flush();

            $this->addFlash('success', 'Réservation annulée avec succès');
        }

        return $this->redirectToRoute('app_reservations');
    }
}