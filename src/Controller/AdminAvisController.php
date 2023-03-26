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
        return $this->render('admin_avis/index.html.twig', [
            'avis' => $avisRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AvisRepository $avisRepository): Response
    {
        $avi = new Avis();
        $avi ->setCreatedAt(new DateTimeImmutable());
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avisRepository->save($avi, true);

            return $this->redirectToRoute('app_admin_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_avis_show', methods: ['GET'])]
    public function show(Avis $avi): Response
    {
        return $this->render('admin_avis/show.html.twig', [
            'avi' => $avi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_avis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avi, AvisRepository $avisRepository): Response
    {
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avisRepository->save($avi, true);

            return $this->redirectToRoute('app_admin_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_avis/edit.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_avis_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avi, AvisRepository $avisRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avi->getId(), $request->request->get('_token'))) {
            $avisRepository->remove($avi, true);
        }

        return $this->redirectToRoute('app_admin_avis_index', [], Response::HTTP_SEE_OTHER);
    }
}
