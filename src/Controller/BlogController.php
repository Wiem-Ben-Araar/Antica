<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentaireRepository ;
use App\Entity\Commentaire ;
use App\Form\CommentaireType ;

#[Route('/blog')]
class BlogController extends AbstractController
{
    #[Route('/', name: 'app_blog_index', methods: ['GET'])]
    public function index(BlogRepository $blogRepository): Response
    {
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
        ]);
    }
    #[Route('/{id}', name: 'app_blog_show', methods: ['GET','POST'])]
    public function show(Request $request,  $id, EntityManagerInterface $entityManager,CommentaireRepository $CommentaireRepository , BlogRepository  $rep ): Response
    {
        $commentaire = new Commentaire();
        $blog = $rep->find($id) ;

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setRelation($blog);
            $text = $form->get('commentaire')->getData();
            $badWords = ['shit', 'fuck', 'omek', 'comment'];
            foreach ($badWords as $word) {
                if (stripos($text, $word) !== false) {
                    $this->addFlash('error', 'Le commentaire contient des propos inappropriés.');
                    $this->addFlash('error', 'Oops! Something went wrong.');
                    return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                }
            }
            $entityManager->persist($commentaire);
            $entityManager->flush();
            return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
        }
        return $this->render('blog/show.html.twig', [
            'blog'=> $blog ,
            'form' => $form->createView(),
        ]);

    }

}
