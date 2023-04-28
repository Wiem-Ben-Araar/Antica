<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeImmutable;

#[Route('/admin/avis')]
class AdminAvisController extends AbstractController
{
    #[Route('/', name: 'app_admin_avis_index', methods: ['GET'])]
    public function index(AvisRepository $avisRepository): Response
    {
        return $this->render('admin_avis/indexadmin.html.twig', [
            'avis' => $avisRepository->findAll(),
        ]);
    }







}
