<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventBackController extends AbstractController
{
    /**
     * @Route("/event/back", name="app_event_back")
     */
    public function index(): Response
    {
        return $this->render('event_back/index.html.twig', [
            'controller_name' => 'EventBackController',
        ]);
    }
}
