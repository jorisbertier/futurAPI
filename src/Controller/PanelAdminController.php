<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panel/admin')]
class PanelAdminController extends AbstractController
{
    #[Route('/', name: 'app_panel_admin')]
    public function index(): Response
    {
        return $this->render('panel_admin/index.html.twig', [
            'controller_name' => 'PanelAdminController',
        ]);
    }
}
