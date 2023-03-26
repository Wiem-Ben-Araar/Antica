<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\Avis1Type;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

#[Route('/avis')]
class AvisController extends AbstractController
{


    #[Route('/new', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AvisRepository $avisRepository): Response
    {
        $avi = new Avis();
        $avi->setCreatedAt(new \DateTimeImmutable());
        $avi->setUser($this->getUser());
        $form = $this->createForm(Avis1Type::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avisRepository->save($avi, true);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }


}
