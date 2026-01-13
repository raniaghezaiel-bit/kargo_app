<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/utilisateurs')]
#[IsGranted('ROLE_ADMIN')]
class UserManagementController extends AbstractController
{
    #[Route('/', name: 'app_admin_users')]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        // Filtrer par type d'utilisateur
        $type = $request->query->get('type', 'all');
        
        $users = $userRepository->findAll();
        
        // Filtrer selon le type
        if ($type !== 'all') {
            $users = array_filter($users, function($user) use ($type) {
                if ($type === 'admin') {
                    return in_array('ROLE_ADMIN', $user->getRoles());
                } elseif ($type === 'passager') {
                    return in_array('ROLE_PASSAGER', $user->getRoles());
                }
                return true;
            });
        }
        
        // Statistiques
        $stats = [
            'total' => $userRepository->count([]),
            'admins' => count(array_filter($userRepository->findAll(), fn($u) => in_array('ROLE_ADMIN', $u->getRoles()))),
            'passagers' => count(array_filter($userRepository->findAll(), fn($u) => in_array('ROLE_PASSAGER', $u->getRoles()))),
        ];
        
        return $this->render('user_management/index.html.twig', [
            'users' => $users,
            'stats' => $stats,
            'current_filter' => $type,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user, ReservationRepository $reservationRepository): Response
    {
        // Récupérer les réservations de l'utilisateur si c'est un passager
        $reservations = [];
        if (in_array('ROLE_PASSAGER', $user->getRoles())) {
            $reservations = $reservationRepository->findBy(['passager' => $user], ['dateReservation' => 'DESC']);
        }
        
        return $this->render('user_management/show.html.twig', [
            'user' => $user,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/{id}/toggle-role', name: 'app_admin_user_toggle_role', methods: ['POST'])]
    public function toggleRole(
        User $user,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if ($this->isCsrfTokenValid('toggle-role'.$user->getId(), $request->request->get('_token'))) {
            $roles = $user->getRoles();
            
            // Ne pas modifier son propre compte
            if ($user === $this->getUser()) {
                $this->addFlash('error', 'Vous ne pouvez pas modifier vos propres rôles');
                return $this->redirectToRoute('app_admin_users');
            }
            
            // Toggle entre ADMIN et PASSAGER
            if (in_array('ROLE_ADMIN', $roles)) {
                $user->setRoles(['ROLE_PASSAGER']);
                $this->addFlash('success', 'Utilisateur rétrogradé en passager');
            } else {
                $user->setRoles(['ROLE_ADMIN']);
                $this->addFlash('success', 'Utilisateur promu en administrateur');
            }
            
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(
        User $user,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Ne pas supprimer son propre compte
            if ($user === $this->getUser()) {
                $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte');
                return $this->redirectToRoute('app_admin_users');
            }
            
            $entityManager->remove($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Utilisateur supprimé avec succès');
        }
        
        return $this->redirectToRoute('app_admin_users');
    }
}