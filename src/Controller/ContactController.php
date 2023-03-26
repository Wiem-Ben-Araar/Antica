<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(request $request, MailerInterface $mailer): Response
    {
        $form=$this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()) {

            $address = $form->get('email')->getData();
            $sujet = $form->get('sujet')->getData();
            $contenu = $form->get('contenu')->getData();

            $email = (new Email())
                ->from($address)
                ->to('admin@admin.com')

                ->subject($sujet)
                ->text($contenu);
            $mailer->send($email);

            return $this->redirectToRoute('app_success');
        }
        return $this->renderForm( 'contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'form'=>$form
        ]);
    }
    #[Route('/conttact/success', name: 'app_success')]
    public function success(): Response
    {
        return $this->render('success/index.html.twig', [
            'controller_name' => 'SuccessController',
        ]);
    }
}
