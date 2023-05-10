<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;

class CommentaireController extends AbstractController
{
    #[Route('/commentaire', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommentaireRepository $commentaireRepository): Response
    {
        $commentaire = new Commentaire();
        $commentaire->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Vérification du commentaire
            $commentaire->setCommentaire($form->get('Commentaire')->getData());
            $badWords = ['shit', 'fuck', 'omek']; // Liste de mauvais mots
            foreach ($badWords as $word) {
                if (stripos($commentaire, $word) !== false) {
                    $this->addFlash('error', 'Le commentaire contient des propos inappropriés.');
                    return $this->redirectToRoute('app_commentaire_new');
                }
            }

            // Enregistrement de l'avis
            $commentaireRepository->save($commentaire, true);

            $this->addFlash('success', 'Votre avis a été enregistré.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ]);
    }
}
