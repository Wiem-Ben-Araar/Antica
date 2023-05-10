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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/panier')]
class PanierController extends AbstractController

{
    #[Route('/', name: 'app_panier_index', methods: ['GET'])]
    public function index(PanierRepository $panierRepository): Response
    {
        $paniers = $panierRepository->findAll();
        $total = 0;
        foreach ($paniers as $panier) {
            $total += $panier->getProduitPrix();
        }

        return $this->render('panier/index.html.twig', [
            'paniers' => $paniers,
            'total' => $total, // Pass the total to the view
        ]);
    }

    #[Route('/new', name: 'app_panier_new', methods: ['GET', 'POST'])]
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

            $entityManager->flush();
            return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier/new.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_panier_show', methods: ['GET'])]
    public function show(Panier $panier): Response
    {
        return $this->render('panier/show.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_panier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Panier $panier, PanierRepository $panierRepository, Security $security, UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $security->getUser(); // Get currently authenticated user
        $panier->setUser($user);
        $panier->setUser($user);


        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $panierRepository->save($panier, true);

            return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier/edit.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_panier_delete', methods: ['POST'])]
    public function delete(Request $request, Panier $panier, PanierRepository $panierRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->request->get('_token'))) {
            $panierRepository->remove($panier, true);
        }

        return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/panier/generate-pdf/{id}", name="panier_generate_pdf")
     */
    public function generatePdf(int $id, FactureController $factureController,PanierRepository $panierRepository): Response

    {
        $panier = $panierRepository->findOneById($id);

        return $factureController->generateLivraisonPdf($panier);
    }

        // ...
        #[Route('/api/panierJson', name: 'panierJson')]
        public function panierJson(Request $request,NormalizerInterface $normalizer): Response
        {
    
            $em = $this->getDoctrine()->getManager()->getRepository(Panier::class); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES
            $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($request->get("idUser")); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

            $data = $em->findBy(["user"=>$user]); 
            $jsonContent =$normalizer->normalize($data, 'json' ,['groups'=>'post:read']);
            return new Response(json_encode($jsonContent));
        }
        #[Route('/api/addToCart', name: 'addToCart')]
        public function addToCart(Request $request,NormalizerInterface $normalizer): Response
        {
    
            $produit = $this->getDoctrine()->getManager()->getRepository(Produits::class)->find($request->get("idProduit")); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES
            $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($request->get("idUser")); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES
            $panier = new Panier();
            $panier->setUser($user);
            $panier->setProduit($produit);
            $panier->setTotal($produit->getPrix());
           
            $entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->persist($panier);
    
            $entityManager->flush();
            $jsonContent =$normalizer->normalize($panier, 'json' ,['groups'=>'post:read']);
            return new Response(json_encode($jsonContent));
        }

        #[Route('/api/deleteProdFromCart/{id}', name: 'deleteProdFromCart')]
        public function deleteProdFromCart(Request $request,NormalizerInterface $normalizer,$id): Response
        {
    
            $panier = $this->getDoctrine()->getManager()->getRepository(Panier::class)->find($id); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES
           // $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($request->get("idUser")); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES
//dd($prod);
            $em = $this->getDoctrine()->getManager();
            //$panier=$em->getRepository(Panier::class)->findOneBy(["user"=>$user,"produit"=>$prod]);
                $em->remove($panier);
                $em->flush();
                $jsonContent =$normalizer->normalize($panier, 'json' ,['groups'=>'post:read']);
                return new Response("information deleted successfully".json_encode($jsonContent));
        }
}
