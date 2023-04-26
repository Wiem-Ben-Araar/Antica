<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer, Security $security): Response
    {
        $user = $security->getUser();

        $form = $this->createForm(ContactType::class, null, ['user' => $user]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sujet = $form->get('sujet')->getData();
            $contenu = $form->get('contenu')->getData();

            $email = (new Email())
                ->from($user->getEmail())
                ->to('admin@admin.com')
                ->subject($sujet)
                ->text($contenu);

            $mailer->send($email);

            return $this->redirectToRoute('app_success');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/contact/success', name: 'app_success')]
    public function success(): Response
    {
        return $this->render('success/index.html.twig', [
            'controller_name' => 'SuccessController',
        ]);
    }
}
