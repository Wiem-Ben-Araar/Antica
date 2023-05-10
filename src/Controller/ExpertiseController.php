<?php

namespace App\Controller;

use App\Entity\Expertise;
use App\Form\ExpertiseType;
use App\Repository\ExpertiseRepository;
use App\Service\QrcodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/expertise')]
class ExpertiseController extends AbstractController
{
    #[Route('/', name: 'app_expertise_index', methods: ['GET'])]
    public function index(): Response
    {
        $expertises = $this->getDoctrine()
            ->getRepository(Expertise::class)
            ->findAll();

        return $this->render('expertise/index.html.twig', [
            'expertises' => $expertises,
        ]);
    }




    #[Route('/create', name: 'app_expertise_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ExpertiseRepository $expertiseRepository): Response
    {

        $form = $this->createForm(ExpertiseType::class, $expertise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expertiseRepository->save($expertise, true);


            return $this->redirectToRoute('app_expertise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('expertise/new.html.twig', [
            'expertise' => $expertise,

        ]);
    }

    #[Route('/{id}', name: 'app_expertise_show', methods: ['GET'])]
    public function show(Expertise $expertise): Response
    {
        return $this->render('expertise/show.html.twig', [
            'expertise' => $expertise,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_expertise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Expertise $expertise, ExpertiseRepository $expertiseRepository): Response
    {

        $form = $this->createForm(ExpertiseType::class, $expertise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expertiseRepository->save($expertise, true);

            return $this->redirectToRoute('app_expertise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('expertise/edit.html.twig', [
            'expertise' => $expertise,
            'form' => $form,

        ]);
    }

    #[Route('/{id}', name: 'app_expertise_delete', methods: ['POST'])]
    public function delete(Request $request, Expertise $expertise, ExpertiseRepository $expertiseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$expertise->getId(), $request->request->get('_token'))) {
            $expertiseRepository->remove($expertise, true);
        }

        return $this->redirectToRoute('app_expertise_index', [], Response::HTTP_SEE_OTHER);
    }
}
