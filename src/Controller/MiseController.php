<?php

namespace App\Controller;

use App\Entity\Mise;
use App\Form\MiseType;
use App\Repository\MiseRepository;
use App\Repository\EnchereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


    /**
     * @Route("/mise")
     */
class MiseController extends AbstractController
{
    /**
     * @Route("/list", name="app_mise_index", methods={"GET"})
     */
    public function index(MiseRepository $miseRepository): Response
    {
        return $this->render('mise/index.html.twig', [
            'mises' => $miseRepository->findAll(),

        ]);
    }

    /**
     * @Route("/new", name="app_mise_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EnchereRepository $enchereRepository, MiseRepository $miseRepository): Response
    {
        $enchere = $enchereRepository->find(1); // replace "1" with the ID of the Enchere you want to use
        $mise = new Mise();
        $form = $this->createForm(MiseType::class, $mise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $miseRepository->add($mise, true);
            return $this->redirectToRoute('app_mise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mise/new.html.twig', [
            'form' => $form->createView(),
            'mises'=>$miseRepository,
            'enchere' => $enchere,
        ]);
    }

    /**
     * @Route("/{id}", name="app_mise_show", methods={"GET"})
     */
    public function show(Mise $mise): Response
    {
        return $this->render('mise/show.html.twig', [
            'mise' => $mise,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_mise_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Mise $mise, MiseRepository $miseRepository): Response
    {
        $form = $this->createForm(MiseType::class, $mise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $miseRepository->add($mise, true);

            return $this->redirectToRoute('app_mise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('mise/edit.html.twig', [
            'mise' => $mise,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_mise_delete", methods={"POST"})
     */
    public function delete(Request $request, Mise $mise, MiseRepository $miseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mise->getId(), $request->request->get('_token'))) {
            $miseRepository->remove($mise, true);
        }

        return $this->render('Mise/_delete_form.html.twig', [
            'mise' => $mise,
        ]);
    }
}
