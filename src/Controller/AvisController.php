<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Avis;
use App\Form\Avis1Type;
use App\Repository\AvisRepository;

class AvisController extends AbstractController
{
    #[Route('/avis/new', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AvisRepository $avisRepository): Response
    {
        $avi = new Avis();
        $avi->setCreatedAt(new \DateTimeImmutable());
        $avi->setUser($this->getUser());
        $form = $this->createForm(Avis1Type::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de la note
            $note = $form->get('note')->getData();
            if ($note < 0 || $note > 20) {
                $this->addFlash('error', 'La note doit être comprise entre 0 et 20.');
                return $this->redirectToRoute('app_avis_new');
            }

            // Vérification du commentaire
            $commentaire = $form->get('commentaire')->getData();
            $badWords = ['shit', 'fuck', 'omek']; // Liste de mauvais mots
            foreach ($badWords as $word) {
                if (stripos($commentaire, $word) !== false) {
                    $this->addFlash('error', 'Le commentaire contient des propos inappropriés.');
                    return $this->redirectToRoute('app_avis_new');
                }
            }

            // Enregistrement de l'avis
            $avisRepository->save($avi, true);

            $this->addFlash('success', 'Votre avis a été enregistré.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form->createView(),
        ]);
    }
}
