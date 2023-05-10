<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PanierRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use  App\Entity\Panier;
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProduitsRepository $produitsRepository): Response
    {
        $produits = $this->getDoctrine()->getRepository(Produits::class)->findAll();

        return $this->render('home/index.html.twig', [
            'produits' => $produits,
        ]);
    }
    #[Route('/ajout_produit_panier', name: 'ajout_produit_panier', methods: ['GET', 'POST'])]
    public function ajoutProduitPanier(Request $request, ProduitsRepository $productRepository, PanierRepository $panierRepository, Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $produitId = $request->request->get('produit_id');
        $produit = $productRepository->find($produitId);
        $panier = new Panier();
        $user = $security->getUser(); // Get currently authenticated user
        $panier->setUser($user);
        $panier->setProduit($produit);
        $panier->setTotal($produit->getPrix());
        $panierRepository->save($panier, true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($panier);

        $entityManager->flush();
        return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);

    }

}
