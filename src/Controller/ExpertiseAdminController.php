<?php

namespace App\Controller;

use App\Entity\Expertise;
use App\Form\ExpertiseType;
use App\Form\SmsType;
use App\Repository\ExpertiseRepository;
use App\Service\Twilio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;

#[Route('/admin/expertise')]
class ExpertiseAdminController extends AbstractController
{
    #[Route('/', name: 'admin_expertise_index', methods: ['GET'])]
    public function index(ExpertiseRepository $expertiseRepository): Response
    {

        return $this->render('expertiseAdmin/index.html.twig', [
            'expertises' => $expertiseRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_expertise_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ExpertiseRepository $expertiseRepository): Response
    {
        $expertise = new Expertise();


        $form = $this->createForm(ExpertiseType::class, $expertise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expertiseRepository->save($expertise, true);

            return $this->redirectToRoute('admin_expertise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('expertiseAdmin/new.html.twig', [
            'expertise' => $expertise,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'admin_expertise_show', methods: ['GET'])]
    public function show(Expertise $expertise): Response
    {
        return $this->render('expertiseAdmin/show.html.twig', [
            'expertise' => $expertise,
        ]);
    }

    /**
     * @throws ConfigurationException
     */
    #[Route('/{id}/send', name: 'admin_expertise_send', methods: ['GET','POST'])]
    public function SendSMS(Request $request, Expertise $expertise, Twilio $twilio): Response
    {
        $form = $this->createForm(SmsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $toNumber = $form->get('phone_number')->getData();
            $msg = $form->get('message')->getData();

            $accountSid = 'AC1e8690b12cd9c32eaae5df36d8b85cd5';
            $authToken = '979e4a1a8f0c8d0441e942f627de0fe0';
            $fromNumber = +16076083908;
            $client = new Client($accountSid, $authToken);
            $message = $twilio->sendMessage($toNumber, $msg);

            return $this->redirectToRoute('admin_expertise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('smss/index.html.twig', [
            'expertise' => $expertise,
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_expertise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Expertise $expertise, ExpertiseRepository $expertiseRepository): Response
    {
        $form = $this->createForm(ExpertiseType::class, $expertise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expertiseRepository->save($expertise, true);

            return $this->redirectToRoute('admin_expertise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('expertiseAdmin/edit.html.twig', [
            'expertise' => $expertise,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'admin_expertise_delete', methods: ['POST'])]
    public function delete(Request $request, Expertise $expertise, ExpertiseRepository $expertiseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$expertise->getId(), $request->request->get('_token'))) {
            $expertiseRepository->remove($expertise, true);
        }

        return $this->redirectToRoute('admin_expertise_index', [], Response::HTTP_SEE_OTHER);
    }
}



