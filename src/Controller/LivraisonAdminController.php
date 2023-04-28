<?php

namespace App\Controller;

use App\Entity\Livraison;
use App\Entity\Panier;
use App\Entity\User;
use App\Form\LivraisonType;
use App\Repository\LivraisonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

#[Route('/admin/livraison')]
class LivraisonAdminController extends AbstractController
{
    #[Route('/', name: 'admin_livraison_index', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('livraisonAdmin/indexadmin.html.twig', [
            'livraisons' => $livraisonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_livraison_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivraisonRepository $livraisonRepository,Security $security, UrlGeneratorInterface $urlGenerator): Response
    {
        $panierRepository = $this->getDoctrine()->getRepository(Panier::class); // Ajouter cette ligne pour obtenir le repository du Panier
        $paniers = $panierRepository->findAll();
        $total = 0;
        foreach ($paniers as $panier) {
            $total += $panier->getProduitPrix();
        }
        $livraison = new Livraison();
        $user = $security->getUser(); // Get currently authenticated user
        $panier->setUser($user);
        $livraison->setUser($user);

        $livraison->setTotal($total);
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraisonRepository->save($livraison, true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($livraison);
            $entityManager->persist($livraison);
            $entityManager->flush();

            return $this->redirectToRoute('admin_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraisonAdmin/newadmin.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
            'paniers' => $paniers, // Pass the paniers to the view
            'total' => $total, // Pass the total to the view
        ]);
    }

    #[Route('/{id}', name: 'admin_livraison_show', methods: ['GET'])]
    public function show(Livraison $livraison): Response
    {
        return $this->render('livraisonAdmin/showadmin.html.twig', [
            'livraison' => $livraison,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_livraison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livraison $livraison, LivraisonRepository $livraisonRepository,Security $security, UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $security->getUser(); // Get currently authenticated user

        $livraison->setUser($user);

        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraisonRepository->save($livraison, true);

            return $this->redirectToRoute('admin_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraisonAdmin/editadmin.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_livraison_delete', methods: ['POST'])]
    public function delete(Request $request, Livraison $livraison, LivraisonRepository $livraisonRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livraison->getId(), $request->request->get('_token'))) {
            $livraisonRepository->remove($livraison, true);
        }

        return $this->redirectToRoute('admin_livraison_index', [], Response::HTTP_SEE_OTHER);
    }

}
