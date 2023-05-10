<?php

namespace App\Controller;
use App\Entity\Produits;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Form\SendMailType;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }
    #[Route('/admin', name: 'app_reclamation_index_front', methods: ['GET'])]
    public function indexAdmin(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/indexAdmin.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        $reclamationRepository->remove($reclamation, true);
        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/email', name: 'sendMailToUser')]
    public function sendEmail(MailerInterface $mailer,Request $request): Response
    {
        $form =$this->createForm(SendMailType::class,null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $message=$form->get('message')->getData();
            $subject=$form->get('subject')->getData();
            $dest=$form->get('dest')->getData();
            $email = (new Email())
                ->from('mohamedali.belhadj@esprit.tn')
                ->to((string)$dest)
                ->subject((string)$subject)
                ->text('Sending emails is fun again!')
                ->html("<p>$message</p>");
            $mailer->send($email);
            $this->addFlash('success', 'votre email a ete bien envoyé');
            return $this->redirectToRoute('app_reclamation_index_front');
        }
        return $this->render('admin/sendMail.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/api/reclamationsJson', name: 'reclamationsJson')]
    public function reclamationsJson(Request $request,NormalizerInterface $normalizer): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Reclamation::class); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

        $data = $em->findAll(); 
        $jsonContent =$normalizer->normalize($data, 'json' ,['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/api/deleteReclamation/{id}', name: 'deleteReclamation')]
    public function deleteReclamation(Request $request,NormalizerInterface $normalizer,$id): Response
    {

        $reclamation = $this->getDoctrine()->getManager()->getRepository(Reclamation::class)->find($id); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES
        $em = $this->getDoctrine()->getManager();

            $em->remove($reclamation);
            $em->flush();
            $jsonContent =$normalizer->normalize($reclamation, 'json' ,['groups'=>'post:read']);
            return new Response("information deleted successfully".json_encode($jsonContent));
    }
    #[Route('/api/addReclamationApi', name: 'addReclamationApi')]
    public function addReclamationApi(NormalizerInterface $Normalizer,Request $request,EntityManagerInterface $entityManager): Response
    {

        $rec = new Reclamation();

        $em = $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Produits::class)->find($request->get('idProduit'));
        $rec->setDescription($request->get('description'));
        $rec->setTitre($request->get('titre'));
        $rec->setState($request->get('state'));
        $rec->setResponse($request->get('reponse'));
        $rec->setDate(new \DateTime());
        $rec->setProduit($prod);
     
        $em->persist($rec);
        $em->flush();
            $jsonContent = $Normalizer->normalize($rec, 'json',['groups'=>'post:read']);
            return new Response(json_encode($jsonContent));

    }

    #[Route('/api/editReclamationApi/{id}', name: 'editReclamationApi')]
    public function editReclamationApi($id,NormalizerInterface $Normalizer,Request $request,EntityManagerInterface $entityManager): Response
    {

        $rec =  $this->getDoctrine()->getManager()->getRepository(Reclamation::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $rec->setDescription($request->get('description'));
        $rec->setTitre($request->get('titre'));
        $rec->setState("resolue");
        $rec->setResponse($request->get('reponse'));
        
      //  $rec->setProduit($prod);
     
        $em->persist($rec);
        $em->flush();
            $jsonContent = $Normalizer->normalize($rec, 'json',['groups'=>'post:read']);
            return new Response(json_encode($jsonContent));

    }
}
