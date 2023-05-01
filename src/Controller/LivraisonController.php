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

#[Route('/livraison')]
class LivraisonController extends AbstractController
{
    #[Route('/', name: 'app_livraison_index', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('livraison/index.html.twig', [
            'livraisons' => $livraisonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_livraison_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivraisonRepository $livraisonRepository, Security $security, UrlGeneratorInterface $urlGenerator): Response
    {
        $panierRepository = $this->getDoctrine()->getRepository(Panier::class);
        $paniers = $panierRepository->findAll();
        $total = 0;
        foreach ($paniers as $panier) {
            $total += $panier->getProduitPrix();
        }

        $livraison = new Livraison();
        $user = $security->getUser();
        $livraison->setUser($user);
        $livraison->setStatut("En cours");
        $livraison->setTotal($total);

        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($livraison);
            $entityManager->flush();

            // Remove the related Panier entities

            $entityManager->flush();

            return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraison/new.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
            'paniers' => $paniers,
            'total' => $total,
        ]);
    }
    #[Route('/{id}', name: 'app_livraison_show', methods: ['GET'])]
    public function show(Livraison $livraison): Response
    {
        return $this->render('livraison/show.html.twig', [
            'livraison' => $livraison,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livraison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livraison $livraison, LivraisonRepository $livraisonRepository, Security $security, UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $security->getUser(); // Récupérez l'utilisateur actuellement authentifié

        $livraison->setUser($user); // Mettez à jour l'utilisateur de la livraison avec l'utilisateur actuel
        $livraison->setStatut("En cours"); // Mettez à jour le statut de la livraison à "En cours"

        // Créez un formulaire pour éditer la livraison
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, sauvegardez la livraison dans la base de données
            $livraisonRepository->save($livraison, true);

            // Redirigez l'utilisateur vers la page d'index des livraisons
            return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        // Créez un formulaire d'annulation de livraison
        $cancelForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_livraison_cancel', ['id' => $livraison->getId()]))
            ->setMethod('POST')
            ->getForm();

        // Rendez la vue 'edit.html.twig' avec les informations de la livraison, le formulaire d'édition et le formulaire d'annulation
        return $this->renderForm('livraison/edit.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
            'cancelForm' => $cancelForm,
        ]);
    }
    #[Route('/{id}', name: 'app_livraison_delete', methods: ['POST'])]
    public function delete(Request $request, Livraison $livraison, LivraisonRepository $livraisonRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livraison->getId(), $request->request->get('_token'))) {
            $livraisonRepository->remove($livraison, true);
        }

        return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/cancel', name: 'app_livraison_cancel', methods: ['GET', 'POST'])]
    public function cancelLivraison(Request $request, Livraison $livraison, LivraisonRepository $livraisonRepository): Response
    {
        // Vérifier si l'utilisateur connecté est le propriétaire de la livraison
        $user = $this->getUser();

            $entityManager = $this->getDoctrine()->getManager();
        // Mettre à jour le statut de la livraison à "annulée"
        $livraison->setStatut("annulée");

        // Enregistrer les changements dans la base de données

        $entityManager->persist($livraison);
        $entityManager->flush();

        // Rediriger vers la page de détails de la livraison
        return $this->redirectToRoute('app_livraison_show', ['id' => $livraison->getId()]);
    }

    /**
     * @Route("/livraison/{id}/pdf", name="livraison_pdf")
     */
    public function generateLivraisonPdf(Livraison $livraison, FactureController $factureController)
    {
        return $factureController->generatePdf($livraison->getId());
    }

    // ...

}
