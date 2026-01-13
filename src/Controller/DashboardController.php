<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();

        // Rediriger selon le rôle
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('dashboard/admin.html.twig');
        }

        // Dashboard passager par défaut
        return $this->render('dashboard/passager.html.twig');
    }

    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDashboard(): Response
    {
        return $this->render('dashboard/admin.html.twig');
    }
}