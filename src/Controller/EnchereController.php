<?php

namespace App\Controller;

use App\Entity\Enchere;
use App\Form\EnchereType;
use App\Repository\EnchereRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/enchere")
 */
class EnchereController extends AbstractController
{
    /**
     * @Route("/list", name="app_enchere_index", methods={"GET"})
     */
    public function index(EnchereRepository $enchereRepository, UserRepository $userRepository, ProduitRepository $produitRepository): Response
    {
        return $this->render('enchere/list.html.twig', [
            'encheres' => $enchereRepository->findAll(),
            'produits' => $produitRepository->findAll(),
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_enchere_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EnchereRepository $enchereRepository): Response
    {
        $enchere = new Enchere();
        $form = $this->createForm(EnchereType::class, $enchere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enchereRepository->add($enchere, true);
            return $this->redirectToRoute('app_enchere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enchere/new.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/{id}", name="app_enchere_show", methods={"GET"})
     */
    public function show(Enchere $enchere): Response
    {
        return $this->render('enchere/show.html.twig', [
            'enchere' => $enchere,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_enchere_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Enchere $enchere, EnchereRepository $enchereRepository): Response
    {
        $form = $this->createForm(EnchereType::class, $enchere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enchereRepository->add($enchere, true);

            return $this->redirectToRoute('app_enchere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enchere/edit.html.twig', [
            'enchere' => $enchere,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_enchere_delete", methods={"POST"})
     */
    public function delete(Request $request, Enchere $enchere, EnchereRepository $enchereRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enchere->getId(), $request->request->get('_token'))) {
            $enchereRepository->remove($enchere, true);
        }

        return $this->redirectToRoute('app_enchere_index', [], Response::HTTP_SEE_OTHER);
    }
}
