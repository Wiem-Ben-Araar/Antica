<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\User;
use App\Entity\Produits;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[Route('/admin/panier')]
class PanierAdminController extends AbstractController
{
    #[Route('/', name: 'admin_panier_index', methods: ['GET'])]
    public function index(PanierRepository $panierRepository): Response
    {
        $paniers = $panierRepository->findAll();
        $total = 0;
        foreach ($paniers as $panier) {
            $total += $panier->getProduitPrix();
        }

        return $this->render('panierAdmin/indexadmin.html.twig', [
            'paniers' => $paniers,
            'total' => $total, // Pass the total to the view
        ]);
    }

    #[Route('/new', name: 'admin_panier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PanierRepository $panierRepository,Security $security, UrlGeneratorInterface $urlGenerator): Response
    {

        $panier = new Panier();
        $user = $security->getUser(); // Get currently authenticated user
        $panier->setUser($user);

        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $panierRepository->save($panier, true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($panier);
            $entityManager->persist($panier);
            $entityManager->flush();
            return $this->redirectToRoute('admin_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panierAdmin/newadmin.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_panier_show', methods: ['GET'])]
    public function show(Panier $panier): Response
    {
        return $this->render('panierAdmin/showadmin.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_panier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Panier $panier, PanierRepository $panierRepository, Security $security, UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $security->getUser(); // Get currently authenticated user
        $panier->setUser($user);
        $panier->setUser($user);


        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $panierRepository->save($panier, true);

            return $this->redirectToRoute('admin_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panierAdmin/editadmin.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_panier_delete', methods: ['POST'])]
    public function delete(Request $request, Panier $panier, PanierRepository $panierRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->request->get('_token'))) {
            $panierRepository->remove($panier, true);
        }

        return $this->redirectToRoute('admin_panier_index', [], Response::HTTP_SEE_OTHER);
    }


}
