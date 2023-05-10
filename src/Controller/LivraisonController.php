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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Another\Namespace\NamedAddress;
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
    #[Route('/api/livraisonsJson', name: 'livraisonsJson')]
    public function livraisonsJson(Request $request,NormalizerInterface $normalizer): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Livraison::class); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

        $data = $em->findAll(); 
        $jsonContent =$normalizer->normalize($data, 'json' ,['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/api/validerLivraisonJson/{id}', name: 'validerLivraisonJson')]
    public function validerLivraisonJson($id,Request $request,NormalizerInterface $normalizer): Response
    {
      
        $em = $this->getDoctrine()->getManager()->getRepository(Livraison::class); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

        $livraison = $em->find($id);
        $panierRepository = $this->getDoctrine()->getRepository(Panier::class);
        $paniers = $panierRepository->findAll();
       

        $entityManager = $this->getDoctrine()->getManager();
        // Mettre à jour le statut de la livraison à "livré"
        $livraison->setStatut("livré");
// Remove the related Panier entities
        foreach ($paniers as $panier) {
            $entityManager->remove($panier);
        }
        $transport = Transport::fromDsn("smtp://pidev.antica@gmail.com:aulfvnkxvwzabctc@smtp.gmail.com:587?encryption=tls");
            $mailer = new Mailer($transport);
           $emailTo = "yosra.shil@esprit.tn" ;
            $email = (new Email())
       
            ->from('pidev.antica@gmail.com')
            ->to($emailTo)
            ->subject('Confirmation!')
            ->text('Sending emails is fun again!')
            ->html('<p>Bonjour , Votre commande est confirmé!!!</p>');   
        

$headers = $email->getHeaders();

$mailer->send($email);
        $entityManager->persist($livraison);
        $entityManager->flush();
        $jsonContent =$normalizer->normalize($livraison, 'json' ,['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/api/annulerLivraisonJson/{id}', name: 'annulerLivraisonJson')]
    public function annulerLivraisonJson($id,Request $request,NormalizerInterface $normalizer): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Livraison::class); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

        $livraison = $em->find($id);
        $livraison->setStatut("Annuler");
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($livraison);
        $entityManager->flush();
        $jsonContent =$normalizer->normalize($livraison, 'json' ,['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/api/addLivraisonJson', name: 'addLivraisonJson')]
    public function addLivraisonJson(Request $request,NormalizerInterface $normalizer): Response
    {

        $panierRepository = $this->getDoctrine()->getRepository(Panier::class);
        $paniers = $panierRepository->findAll();
        $total = 0;
        foreach ($paniers as $panier) {
            $total += $panier->getProduitPrix();
        }

        $livraison = new Livraison();
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get("idUser"));
        $livraisonDate = new \DateTime();
        $livraisonDate->modify('+2 days');
        $livraison->setDateLivraison($livraisonDate);
        $livraison->setUser($user);
        $livraison->setStatut("En cours");
        $livraison->setTotal($total);
       $livraison->setAdresse($request->get("adresse"));
       $entityManager = $this->getDoctrine()->getManager();
$entityManager->persist($livraison);
$entityManager->flush();
        $jsonContent =$normalizer->normalize($livraison, 'json' ,['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/api/editLivraisonJson/{id}', name: 'editLivraisonJson')]
    public function editLivraisonJson($id,Request $request,NormalizerInterface $normalizer): Response
    {

       

        $livraison =$this->getDoctrine()->getManager()->getRepository(Livraison::class)->find($id);
       
        $livraison->setStatut("En cours");
        
       $livraison->setAdresse($request->get("adresse"));
       $entityManager = $this->getDoctrine()->getManager();
$entityManager->persist($livraison);
$entityManager->flush();
        $jsonContent =$normalizer->normalize($livraison, 'json' ,['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
}
