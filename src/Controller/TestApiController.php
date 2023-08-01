<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestApiController extends AbstractController
{
    #[Route('/api/test_connection', name: 'app_test_api')]
    public function index(): Response
    {
    $user = $this->getUser();

    if($user === null) {
        return $this->json('pas connecté');
    } else {

        return $this->json('connecté en tant que ' . $user->getEmail());  
    }
    }
}
