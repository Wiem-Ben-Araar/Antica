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
use Vich\UploaderBundle\Handler\UploadHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/produits')]
class ProduitsController extends AbstractController
{
    #[Route('/', name: 'app_produits_index', methods: ['GET'])]
    public function index(ProduitsRepository $produitsRepository): Response
    {
        return $this->render('produits/index.html.twig', [
            'produits' => $produitsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_produits_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProduitsRepository $produitsRepository, UploadHandler $uploadHandler): Response
    {
        $produit = new Produits();
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadHandler->upload($produit, 'imageFile');
            $produitsRepository->save($produit, true);

            return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produits/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produits_show', methods: ['GET'])]
    public function show(Produits $produit): Response
    {
        return $this->render('produits/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produits_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produits $produit, ProduitsRepository $produitsRepository): Response
    {
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitsRepository->save($produit, true);

            return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produits/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produits_delete', methods: ['POST'])]
    public function delete(Request $request, Produits $produit, ProduitsRepository $produitsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $produitsRepository->remove($produit, true);
        }

        return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
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
    #[Route('/api/produitsJson', name: 'produitsJson')]
    public function produitsJson(Request $request,NormalizerInterface $normalizer): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Produits::class); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

        $data = $em->findAll(); 
        $jsonContent =$normalizer->normalize($data, 'json' ,['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/api/produitsSearchJson', name: 'produitsSearchJson')]
    public function produitsSearchJson(Request $request,NormalizerInterface $normalizer): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Produits::class); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES
        $search = $request->get("search");
        $data = $em->findBy(["nom"=>$search]); 
        $jsonContent =$normalizer->normalize($data, 'json' ,['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/api/deleteProdJson/{id}', name: 'deleteProdJson')]
    public function deleteProdJson(Request $request,NormalizerInterface $normalizer,$id): Response
    {

        $prod = $this->getDoctrine()->getManager()->getRepository(Produits::class)->find($id); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES
        $em = $this->getDoctrine()->getManager();

            $em->remove($prod);
            $em->flush();
            $jsonContent =$normalizer->normalize($prod, 'json' ,['groups'=>'post:read']);
            return new Response("information deleted successfully".json_encode($jsonContent));
    }

    #[Route('/api/addProduitJson', name: 'addProduitJson')]
    public function addProduitJson(NormalizerInterface $Normalizer,Request $request,EntityManagerInterface $entityManager): Response
    {

        $produit = new Produits();

        $em = $this->getDoctrine()->getManager();
        $produit->setNom($request->get('nom'));
        $produit->setPrix($request->get('prix'));
        $produit->setGenre($request->get('genre'));
        
        $produit->setImage("645665b81a2fd955008072.jpg");
     
        $em->persist($produit);
        $em->flush();
            $jsonContent = $Normalizer->normalize($produit, 'json',['groups'=>'post:read']);
            return new Response(json_encode($jsonContent));

    }

    #[Route('/api/editProduitJson/{id}', name: 'editProduitJson')]
    public function editProduitJson($id,NormalizerInterface $Normalizer,Request $request,EntityManagerInterface $entityManager): Response
    {
        $em = $this->getDoctrine()->getManager();
        $produit =$em->getRepository(Produits::class)->find($id);
        $produit->setNom($request->get('nom'));
        $produit->setPrix($request->get('prix'));
        $produit->setGenre($request->get('genre'));
        
        $produit->setImage("645665b81a2fd955008072.jpg");
     
        $em->persist($produit);
        $em->flush();
            $jsonContent = $Normalizer->normalize($produit, 'json',['groups'=>'post:read']);
            return new Response(json_encode($jsonContent));

    }

}
