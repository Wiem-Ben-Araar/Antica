<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Repository\PanierRepository;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/produits')]
class ProduitsAdminController extends AbstractController
{
    #[Route('/', name: 'admin_produits_index', methods: ['GET'])]
    public function index(ProduitsRepository $produitsRepository): Response
    {
        return $this->render('produitsAdmin/indexadmin.html.twig', [
            'produits' => $produitsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_produits_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProduitsRepository $produitsRepository): Response
    {
        $produit = new Produits();
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitsRepository->save($produit, true);

            return $this->redirectToRoute('admin_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produitsAdmin/newadmin.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_produits_show', methods: ['GET'])]
    public function show(Produits $produit): Response
    {
        return $this->render('produitsAdmin/showadmin.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_produits_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produits $produit, ProduitsRepository $produitsRepository): Response
    {
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitsRepository->save($produit, true);

            return $this->redirectToRoute('admin_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produitsAdmin/editadmin.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_produits_delete', methods: ['POST'])]
    public function delete(Request $request, Produits $produit, ProduitsRepository $produitsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $produitsRepository->remove($produit, true);
        }

        return $this->redirectToRoute('admin_produits_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/get-price', name: 'get_price', methods: ['POST'])]
    public function getPrice(Request $request, PanierRepository $panierRepository): Response
    {
        $productId = $request->request->get('product_id'); // Récupérer l'identifiant du produit envoyé par le formulaire

        // Rechercher le produit dans le repository
        $produit = $this->getDoctrine()->getRepository(Produits::class)->find($productId);

        // Vérifier si le produit existe
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable');
        }

        // Récupérer le prix du produit
        $prix = $produit->getPrix();

        // Retourner le prix sous forme de JSON
        return $this->json(['prix' => $prix]);
    }
}
